<?php
/*
Plugin Name: Frontity
Plugin URI: https://wordpress.org/plugins/wordpress-pwa/
Description: WordPress plugin to turn WordPress blogs into Progressive Web Apps.
Version: 1.8.0
Author: Frontity
Author URI: https://frontity.com/?utm_source=plugin-repository&utm_medium=link&utm_campaign=plugin-description
License: GPL v3
Copyright: Worona Labs SL
 */

// Define the directory seperator if it isn't already
if (!defined('DS')) {
	if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
		define('DS', '\\');
	} else {
		define('DS', '/');
	}
}

if (!class_exists('frontity')) :

class frontity {
	// vars
	public $plugin_version = '1.8.0';
	public $rest_api_installed = false;
	public $rest_api_active = false;
	public $rest_api_working = false;

	/*
	 *  Constructor
	 *
	 *  This function will construct all the neccessary actions, filters and functions for the Frontity plugin to work
	 *
	 *  @type	function
	 *  @date	10/06/14
	 *  @since	0.6.0
	 *
	 *  @param	N/A
	 *  @return	N/A
	 */

	function __construct() {
		// actions
		add_action('init', array($this, 'init'), 1);
		add_action('admin_menu', array($this, 'render_frontity_admin')); //add the admin page
		add_action('admin_init', array($this, 'frontity_register_settings')); //register the settings
		add_action('admin_notices', array($this, 'frontity_admin_notices')); //Display the validation errors and update messages

		add_action('wp_ajax_frontity_save_settings', array($this, 'save_settings'));
		// add_action('wp_ajax_wp_pwa_purge_htmlpurifier_cache', array($this,'purge_htmlpurifier_cache'));

		add_action('plugins_loaded', array($this, 'wp_rest_api_plugin_is_installed'));
		add_action('plugins_loaded', array($this, 'wp_rest_api_plugin_is_active'));
		add_action('init', array($this, 'allow_origin'));

		add_action('admin_enqueue_scripts', array($this, 'register_frontity_scripts'));

		add_action('rest_api_init', array($this, 'rest_routes'));
		add_action('registered_post_type', array($this, 'add_custom_post_types_filters'));

		add_action('wp_head', array($this, 'amp_add_canonical'));

		add_action('embed_footer', array($this, 'send_post_embed_height'));

		add_filter('wp_get_attachment_link', array($this, 'add_id_to_gallery_images'), 10, 2);
		add_filter('wp_get_attachment_image_attributes', array($this, 'add_id_to_gallery_image_attributes'), 10, 2);
	}

	function init() {}

	function save_settings() {
		$data = json_decode(stripslashes($_POST["data"]), true);

		if ($data) {
			update_option(
				'frontity_settings',
				$data
			);
		}

		wp_send_json($data);
	}

	function send_post_embed_height() {
?>
		<script>
			window.parent.postMessage({
				sentinel: 'amp',
				type: 'embed-size',
				height: document.body.scrollHeight
			}, '*');
		</script>
<?php
	}

	function update_image_id_transient_keys($new_transient_key) {
		$transient_keys = get_option('image_id_transient_keys');
		$transient_keys[] = $new_transient_key;
		update_option('image_id_transient_keys', $transient_keys);
	}

	function purge_image_id_transient_keys() {
		$transient_keys = get_option('image_id_transient_keys');
		foreach ($transient_keys as $t) {
			delete_transient($t);
		}
		update_option('image_id_transient_keys', array());
	}

	function add_id_to_gallery_image_attributes($attrs, $attachment) {
		$attrs['data-attachment-id'] = $attachment->ID;
		$attrs['data-attachment-id-source'] = 'image-attributes-hook';
		return $attrs;
	}

	function add_id_to_gallery_images($html, $attachment_id) {
		$attachment_id = intval($attachment_id);
		$html = str_replace(
			'<img ',
			sprintf(
				'<img data-attachment-id="%1$d" data-attachment-id-source="attachment-link-hook"',
				$attachment_id
			),
			$html
		);
		$html = apply_filters('jp_carousel_add_data_to_images', $html, $attachment_id);
		return $html;
	}

	function add_custom_post_types_filters($post_type) {
		add_filter('rest_prepare_' . $post_type, array($this, 'purify_html'), 9, 3);
		add_filter('rest_prepare_' . $post_type, array($this, 'add_latest_to_links'), 10);
		add_filter('rest_prepare_' . $post_type, array($this, 'add_image_ids'), 10, 3);
		register_rest_field(
			$post_type,
			'latest',
			array(
				'get_callback' => array($this, 'wp_api_get_latest'),
				'schema' => null,
			)
		);
	}

	function wp_api_get_latest($p) {
		$types = apply_filters('add_custom_post_types_to_latest', array($p['type']));
		return $types;
	}

	function rest_routes() {
		register_rest_route('wp-pwa/v1', '/siteid/', array(
			'methods' => 'GET',
			'callback' => array($this, 'get_wp_pwa_site_id')
		));
		register_rest_route('wp-pwa/v1', '/discover/', array(
			'methods' => 'GET',
			'callback' => array($this, 'discover_url')
		));
		register_rest_route('wp-pwa/v1', '/plugin-version/', array(
			'methods' => 'GET',
			'callback' => array($this, 'get_wp_pwa_plugin_version')
		));
		register_rest_route('wp-pwa/v1', '/site-info/', array(
			'methods' => 'GET',
			'callback' => array($this, 'get_site_info')
		));
		register_rest_route('wp/v2', '/latest/', array(
			'methods' => 'GET',
			'callback' => array($this, 'latest_general_endpoint')
		));
		register_rest_route('wp/v2', '/latest/(?P<id>\w+)', array(
			'methods' => 'GET',
			'callback' => array($this, 'latest_individual_endpoint'),
			'args' => array(
				'id' => array(
					'validate_callback' => function ($param) {
						return post_type_exists($param);
					}
				)
			)
		));
	}

	function get_latest_from_cpt($cpts) {
		$result = array();
		foreach ($cpts as &$cpt) {
			if (post_type_exists($cpt)) {
				$cpt_object = get_post_type_object($cpt);
				if ($cpt_object->show_in_rest) {
					if ($cpt === 'post' &&
						get_option('show_on_front') === 'page' &&
						get_option('frontity_settings')['wp_pwa_force_frontpage']) {
						$link = get_option('home');
					} else {
						$link = get_post_type_archive_link($cpt);
					}
					$data = array(
						"id" => $cpt,
						"link" => $link,
						"count" => intval(wp_count_posts($cpt)->publish),
						"name" => $cpt_object->label,
						"slug" => $cpt_object->name,
						"taxonomy" => 'latest'
					);
					if ($cpt === 'post') $data['name'] = get_bloginfo('name');
					$result[] = apply_filters('rest_prepare_latest', $data);
				}
			}
		}
		return $result;
	}

	function latest_individual_endpoint($data) {
		$cpts = apply_filters(
			'add_custom_post_types_to_latest',
			array($cpt = $data->get_url_params()['id'])
		);
		return $this->get_latest_from_cpt($cpts);
	}

	function latest_general_endpoint($data)	{
		$params = $data->get_params();
		foreach ($params as $params_cpt => $params_id) {
			if (post_type_exists($params_cpt)) {
				$cpt = $params_cpt;
			}
		}
		if (!isset($cpt)) {
			$cpts = apply_filters('add_custom_post_types_to_latest', get_post_types());
			return $this->get_latest_from_cpt($cpts);
		}
		$cpts = apply_filters('add_custom_post_types_to_latest', array($cpt));
		return $this->get_latest_from_cpt($cpts);
	}

	function add_latest_to_links($data)	{
		$type = $data->data['type'];
		$id = $data->data['id'];
		$terms_url = add_query_arg(
			$type,
			$id,
			rest_url('wp/v2/latest')
		);
		$data->add_links(array(
			'https://api.w.org/term' => array(
				'href' => $terms_url,
				'taxonomy' => 'latest',
				'embeddable' => true,
			)
		));
		return $data;
	}

	function get_attachment_id($url) {
		$transient_name = 'frt_' . md5($url);
		$attachment_id = get_transient($transient_name);
		$transient_miss = $attachment_id === false;

		if ($transient_miss) {
			$attachment_id = 0;
			$dir = wp_upload_dir();
			$uploadsPath = parse_url($dir['baseurl'])['path'];
			$isInUploadDirectory = strpos($url, $uploadsPath . '/') !== false;
			$wpHost = parse_url($dir['baseurl'])['host'];
			$isNotExternalDomain = strpos($url, $wpHost . '/') !== false;
			if ($isInUploadDirectory && $isNotExternalDomain) {
				$file = basename(urldecode($url));
				$query_args = array(
					'post_type' => 'attachment',
					'post_status' => 'inherit',
					'fields' => 'ids',
					'meta_query' => array(
						array(
							'value' => $file,
							'compare' => 'LIKE',
							'key' => '_wp_attachment_metadata',
						),
					)
				);
				$query = new WP_Query($query_args);
				if ($query->have_posts()) {
					foreach ($query->posts as $post_id) {
						$meta = wp_get_attachment_metadata($post_id);
						$original_file = basename($meta['file']);
						$cropped_image_files = wp_list_pluck($meta['sizes'], 'file');
						if ($original_file === $file || in_array($file, $cropped_image_files)) {
							$attachment_id = $post_id;
							break;
						}
					}
				}
			}

			set_transient($transient_name, $attachment_id, 0); // never expires
			$this->update_image_id_transient_keys($transient_name);
		}

		return array(
			'id' => intval($attachment_id),
			'miss' => $transient_miss,
		);
	}

	function fix_forbidden_media($id)	{
		if (!$id) return;

		$id = (int)$id;
		$attachment = get_post($id);
		if ($attachment->post_type !== 'attachment') return;

		$parent = get_post($attachment->post_parent);
		if ($parent && $parent->post_status !== 'publish') {
			wp_update_post(
				array(
					'ID' => $id,
					'post_parent' => 0,
				)
			);
		}
	}

	function add_image_ids($data, $post_type, $request)	{
		global $wpdb;

		$purge = $request->get_param('purgeContentMediaTransients') === 'true';
		$fixForbiddenMedia = $request->get_param('fixForbiddenMedia') === 'true';

		if (!class_exists('simple_html_dom')) {
			require_once('libs/simple_html_dom.php');
		}
				
				// remove image ids stored in transients if requested
		if ($purge) $this->purge_image_id_transient_keys();
				
				// fix featured media if necessary
		if ($fixForbiddenMedia)
			$this->fix_forbidden_media($data->data['featured_media']);

		$dom = new simple_html_dom();
		$dom->load($data->data['content']['rendered']);
		$imgIds = [];
		foreach ($dom->find('img') as $image) {
			$dataAttachmentId = $image->getAttribute('data-attachment-id');
			$class = $image->getAttribute('class');
			preg_match('/\bwp-image-(\d+)\b/', $class, $wpImage);
			if ($dataAttachmentId) {
				$imgIds[] = intval($dataAttachmentId);
			} elseif ($wpImage && isset($wpImage[1])) {
				$image->setAttribute('data-attachment-id', $wpImage[1]);
				$image->setAttribute('data-attachment-id-source', 'wp-image-class');
				$imgIds[] = intval($wpImage[1]);
			} else {
				$result = $this->get_attachment_id($image->src);
				$id = $result['id'];
				$miss = $result['miss'];
				$image->setAttribute('data-attachment-id-source', 'wp-query-transient-' . ($miss ? 'miss' : 'hit'));
				if ($id !== 0) {
					$image->setAttribute('data-attachment-id', $id);
					$imgIds[] = intval($id);
				}
			}
		}
		if (sizeof($imgIds) > 0) {
					// Fix content media if necessary
			if ($fixForbiddenMedia)
				foreach ($imgIds as $imgId) $this->fix_forbidden_media($imgId);

			$media_url = add_query_arg(
				array(
					'include' => join(',', $imgIds),
					'per_page' => sizeof($imgIds),
				),
				rest_url('wp/v2/media')
			);
			$data->add_links(array(
				'wp:contentmedia' => array(
					'href' => $media_url,
					'embeddable' => true,
				)
			));
		}
		$html = $dom->save();
		if ($html) $data->data['content']['rendered'] = $html;
		$data->data['content_media'] = $imgIds;
		return $data;
	}

	function purify_html($data, $post_type, $request)	{
		$disableHtmlPurifier = $request->get_param('disableHtmlPurifier');

		if ($disableHtmlPurifier === 'true') return $data;

		$data->data['title']['text'] =
			strip_tags(html_entity_decode($data->data['title']['rendered']));
		$data->data['excerpt']['text'] =
			strip_tags(html_entity_decode($data->data['excerpt']['rendered']));

		require_once(plugin_dir_path(__FILE__) . '/libs/purifier.php');
		$purifier = load_purifier();
		$purifiedContent = $purifier->purify($data->data['content']['rendered']);
		if (!empty($purifiedContent)) {
			$data->data['content']['rendered'] = $purifiedContent;
		}

		return $data;
	}

			/*
	*  init
	*
	*  This function is called during the 'init' action and will do things such as:
	*  create custom_post_types, register scripts, add actions / filters
	*
	*  @type	action (init)
	*  @date	10/06/14
	*  @since	0.6.0
	*
	*  @param	N/A
	*  @return	N/A
	*/

	function rrmdir($dir)	{
		if (is_dir($dir)) {
			$objects = scandir($dir);
			foreach ($objects as $object) {
				if ($object != "." && $object != "..") {
					if (filetype($dir . DS . $object) == "dir") {
						rrmdir($dir . DS . $object);
					} else {
						unlink($dir . DS . $object);
					}
				}
			}
			reset($objects);
			rmdir($dir);
		}
	}

	function purge_htmlpurifier_cache()	{
		$upload = wp_upload_dir();
		$upload_base = $upload['basedir'];
		$htmlpurifier_dir = $upload_base . DS . 'frontity' . DS . 'htmlpurifier';
		$this->rrmdir($htmlpurifier_dir . DS . 'HTML');
		$this->rrmdir($htmlpurifier_dir . DS . 'CSS');
		$this->rrmdir($htmlpurifier_dir . DS . 'URI');
		wp_send_json(array(
			'status' => 'ok',
		));
	}

	function frontity_admin_notices()	{
		settings_errors();
	}

	function frontity_register_settings()	{
		register_setting(
			'frontity_settings',
			'frontity_settings',
			array($this, 'frontity_settings_validator')
		);
	}

	/**
	 * Register and enqueue scripts.
	 */
	public function register_frontity_scripts($hook) {
		if (
			'toplevel_page_frontity_home' === $hook ||
			'frontity_page_frontity_settings' === $hook
		) {
			wp_register_script(
				'frontity_admin_js',
				plugin_dir_url(__FILE__) . 'admin/dist/main.js',
				array(),
				null,
				true
			);
			wp_enqueue_script('frontity_admin_js');
		}
	}

  /*
	*  render_frontity_admin
	*
	*  This function is called by the 'frontity_admin_actions' function and will do things such as:
	*  add a wp_pwa page to render the admin content
	*
	*  @type	fucntion called by 'frontity_admin_actions'
	*  @date	18/07/14
	*  @since	0.6.0
	*
	*  @param	N/A
	*  @return	N/A
	*/

	function render_frontity_admin() {
		$icon_url = trailingslashit(plugin_dir_url(__FILE__)) . "admin/assets/frontity_20x20.png";
		$position = 64.999989; //Right before the "Plugins"

		add_menu_page(
			'Frontity',
			'Frontity',
			1,
			'frontity_home',
			function() {
				include('admin/index.php');
			},
			$icon_url,
			$position
		);

		add_submenu_page(
			'frontity_home',
			'Frontity - Settings',
			'Settings',
			1,
			'frontity_settings',
			function() {
				include('admin/index.php');
			}
		);
	}

	function get_wp_pwa_site_id() {
		$settings = get_option('frontity_settings');

		if (isset($settings['wp_pwa_siteid'])) {
			$wp_pwa_site_id = $settings["wp_pwa_siteid"];
		} else {
			$wp_pwa_site_id = null;
		}

		return array('siteId' => $wp_pwa_site_id);
	}

	function get_wp_pwa_plugin_version() {
		return array('plugin_version' => $this->plugin_version);
	}

	function get_site_info() {
		$homepage_title = get_bloginfo('name');
		$homepage_metadesc = get_bloginfo('description');
		$homepage_url = get_bloginfo('url');
		$per_page = get_option('posts_per_page');

		$site_info = array(
			'homepage_title' => $homepage_title,
			'homepage_metadesc' => $homepage_metadesc,
			'homepage_url' => $homepage_url,
			'per_page' => $per_page
		);

		if (has_filter('wp_pwa_get_site_info')) {
			$site_info = apply_filters('wp_pwa_get_site_info', $site_info);
		}

		return array(
			'home' => array(
				'title' => $site_info['homepage_title'],
				'description' => $site_info['homepage_metadesc'],
				'url' => $site_info['homepage_url']
			),
			'perPage' => $site_info['per_page']
		);
	}

	/*
	*	@param \WP_REST_Request $request Full details about the request
	*/
	function discover_url($request)	{
		$first_folder = $request['first_folder'];
		$last_folder = $request['last_folder'];

		if (is_null($last_folder)) {
			return array('Error' => 'last_folder is missing');
		}

				// ----------------
				// Post
				// ----------------
		$args = array(
			'name' => $last_folder,
			'numberposts' => 1,
		);
		$post = get_posts($args);
		if (sizeof($post) > 0) {
			return $post[0];
		}

				// ----------------
				// Page
				// ----------------
		$args = array(
			'name' => $last_folder,
			'numberposts' => 1,
			'post_type' => 'page',
		);
		$page = get_posts($args);
		if (sizeof($page) > 0) {
			return $page[0];
		}

				// ----------------
				// Author
				// ----------------
		if ($first_folder === 'author') {
			$args = array(
				'author_name' => $last_folder,
			);
			$author = get_posts($args);
			if (sizeof($author) > 0) {
				return $author[0];
			} else {
				return (new stdClass()); //empty object instead of null
			}
		}

				// ----------------
				// Category
				// ----------------
		$category = get_term_by('slug', $last_folder, 'category');
		if ($category) {
			return $category;
		}

				// ----------------
				// Tag
				// ----------------
		$tag = get_term_by('slug', $last_folder, 'tag');
		if ($tag) {
			return $tag;
		}

				// ----------------
				// Custom Post type
				// ----------------

		$post_types = get_post_types('', 'object');
		$post_type = '';

		foreach ($post_types as $p) {
			if ($p->rewrite['slug'] == $first_folder) {
				$post_type = $p->name;
			}
		}

		if ($post_type !== '') {
			$args = array(
				'name' => $last_folder,
				'numberposts' => 1,
				'post_type' => $post_type,
			);
			$custom_post = get_posts($args);

			if (sizeof($custom_post) > 0) {
				return $custom_post[0];
			}
		}

				// ----------------
				// Custom Taxonomy
				// ----------------
		$taxonomies = get_taxonomies('', 'object');
		$taxonomy = '';

		foreach ($taxonomies as $t) {
			if ($t->rewrite['slug'] === $first_folder) {
				$taxonomy = $t->name;
			}
		}

		if ($taxonomy === '') {
			return array('Error' => $first_folder . ' not supported');
		}

		$custom_taxonomy = get_term_by('slug', $last_folder, $taxonomy);

		if ($custom_taxonomy) {
			return $custom_taxonomy;
		} else {
			return array('Error' => $first_folder . 'not supported');
		}

				// ----------------
				// first_folder not found
				// ----------------
		return array('Error' => $last_folder . ' not found');
	}

			//Checks if the rest-api plugin is installed
	public function wp_rest_api_plugin_is_installed() {
		if (!function_exists('get_plugins')) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$plugins = get_plugins();

		$this->rest_api_installed = isset($plugins['rest-api/plugin.php']);
	}

			//Checks if the rest-api plugin is active
	public function wp_rest_api_plugin_is_active() {
		$this->rest_api_active = class_exists('WP_REST_Controller');
	}

	//Generates the url to 'auto-activate' the rest-api plugin
	public function get_activate_wp_rest_api_plugin_url() {
		$plugin = 'rest-api/plugin.php';
		$plugin_escaped = str_replace('/', '%2F', $plugin);

		$activateUrl = sprintf(admin_url('plugins.php?action=activate&plugin=%s&plugin_status=all&paged=1&s'), $plugin_escaped);

				// change the plugin request to the plugin to pass the nonce check
		$_REQUEST['plugin'] = $plugin;
		$activateUrl = wp_nonce_url($activateUrl, 'activate-plugin_' . $plugin);

		return $activateUrl;
	}

	//Adds Cross origin * to the header
	function allow_origin() {
		if (!headers_sent()) header("Access-Control-Allow-Origin: *");
	}

	//Checks if the json posts endpoint is responding correctly
	function wp_rest_api_endpoint_works() {
		$rest_api_url = get_site_url() . '/wp-json/wp/v2/posts';
		$args = array('timeout' => 10, 'httpversion' => '1.1');

		$response = wp_remote_get($rest_api_url, $args);

		if (is_array($response)) {
			$body = $response['body'];
			$code = $reponse['reponse']['code'];
			$message = $reponse['reponse']['message'];
			$json_reponse = json_decode($body);

			//CHECKS
			// $code != 200
			// json valid
			// json without error message { code: "rest_no_route" }
		} else {
			return false;
		}
	}

	//Injects the amp URL to the header
	public function amp_add_canonical() {
		$settings = get_option('frontity_settings');
		$prettyPermalinks = get_option('permalink_structure') !== '';
		$url = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST']
			. $_SERVER['REQUEST_URI'];
		$initialUrl = $prettyPermalinks ? strtok($url, '?') : $url;
		$wp_pwa_amp = $settings['wp_pwa_amp'];
		$ampServer = $settings['wp_pwa_amp_server'];
		$ampForced = false;
		$dev = 'false';
		$excludes = isset($settings['wp_pwa_excludes']) ? $settings['wp_pwa_excludes'] : array();
		$exclusion = false;

		if (sizeof($excludes) !== 0) {
			foreach ($excludes as $regex) {
				$output = array();
				$regex = str_replace('/', '\/', $regex);
				preg_match('/' . $regex . '/', $url, $output);
				if (sizeof($output) > 0) {
					$exclusion = true;
				}
			}
		}

		if (isset($_GET['amp']) && $_GET['amp'] === 'true') {
			$ampForced = true;
			$dev = 'true';
		}
		if (isset($_GET['ampUrl'])) {
			$ampServer = $_GET['ampUrl'];
			$dev = 'true';
		}
		if (isset($_GET['dev'])) $dev = $_GET['dev'];

				//posts
		if ($ampForced || (isset($wp_pwa_amp) && ($wp_pwa_amp !== 'disabled') && (is_single()) && $exclusion === false)) {
			$id = get_queried_object()->ID;
			$type = get_queried_object()->post_type;
			$permalink = get_permalink($id);
			$path = parse_url($permalink, PHP_URL_PATH);
			$query = '?siteId=' . $settings["wp_pwa_siteid"]
				. '&env=' . $settings['wp_pwa_env']
				. '&dev=' . $dev
				. '&type=' . $type
				. '&id=' . $id
				. '&initialUrl=' . $initialUrl;
			$amp_url = $ampServer . $path . $query;

			printf('<link rel="amphtml" href="%s" />', $amp_url);
			printf("\n");
		}
	}
}

/*
 *  frontity
 *
 *  The main function responsible for returning the one true frontity Instance to functions everywhere.
 *  Use this function like you would a global variable, except without needing to declare the global.
 *
 *  Example: <?php frontity = frontity(); ?>
 *
 *  @type	function
 *  @date	11/06/14
 *  @since	0.6.0
 *
 *  @param	N/A
 *  @return	(object)
 */

function frontity() {
	global $frontity;

	if (!isset($frontity)) $frontity = new frontity();
	

	if (class_exists('WP_REST_Controller')) {
		require_once('libs/class-rest-api-filter-fields.php');
	}

	$GLOBALS['frontity_path'] = '/' . basename(plugin_dir_path(__FILE__));

	return $frontity;
}

// initialize
frontity();

function frontity_activation() {
	$defaults = array(
		"site_id_requested" => false,
		"site_id" => "",
		"pwa_active" => false,
		"amp_active" => false,
		"ssr_server" => "https://ssr.wp-pwa.com",
		"static_server" => "https://static.wp-pwa.com",
		"amp_server" => "https://amp.wp-pwa.com",
		"frontpage_forced" => false,
		"html_purifier_active" => false,
		"excludes" => array(),
		"api_filters" => array(),
	);

	$settings = get_option('frontity_settings');

	if ($settings) {
		// Remove deprecated settings.
		$valid_settings = array_intersect_key($settings, $defaults);
		// Replace defaults with existing settings.
		$defaults = array_replace($defaults, $valid_settings);
	}

	update_option('frontity_settings', $defaults);

	flush_rewrite_rules();

	$upload = wp_upload_dir();
	$upload_base = $upload['basedir'];
	$frontity_dir = $upload_base . DS . 'frontity';

	if (!is_dir($frontity_dir)) {
		mkdir($frontity_dir, 0755);
		if (is_dir($frontity_dir)) {
			file_put_contents($frontity_dir . DS . 'index.php', "<?php\r\n// Silence is golden\r\n?>");
			$htmlpurifier_dir = $frontity_dir . DS . 'htmlpurifier';
			mkdir($htmlpurifier_dir, 0755);
			if (is_dir($htmlpurifier_dir)) {
				file_put_contents($htmlpurifier_dir . DS . 'index.php', "<?php\r\n// Silence is golden\r\n?>");
			}
		}
	}
}

function frontity_deactivation() {
	delete_option('frontity_settings');
}

function frontity_update() {}

function frontity_uninstallation() {}

register_activation_hook(__FILE__, 'frontity_activation');
register_deactivation_hook(__FILE__, 'frontity_deactivation');

endif; // class_exists check