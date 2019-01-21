<?php
/*
Plugin Name: Frontity
Plugin URI: https://wordpress.org/plugins/wordpress-pwa/
GitHub Plugin URI: https://github.com/frontity/wp-plugin
Description: WordPress plugin to turn WordPress blogs into Progressive Web Apps.
Version: 1.11.0
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

class Frontity {
	public $plugin_version = '1.11.0';

	function __construct() {
		// Migrates settings when the plugin updates.
		add_action('upgrader_process_complete', array($this, 'plugin_update_completed'));
		// Adds the admin pages to the menu.
		add_action('admin_menu', array($this, 'render_frontity_admin'));
		// Resgisters the settings.
		add_action('admin_init', array($this, 'frontity_register_settings'));
		// Displays the validation erros and update messages.
		add_action('admin_notices', array($this, 'frontity_admin_notices'));

		// Saves a snapshot of settings in WP db.
		add_action('wp_ajax_frontity_save_settings', array($this, 'save_settings'));
		// Purges HTMLPurifier cache.
		add_action('wp_ajax_frontity_purge_htmlpurifier_cache', array($this,'purge_htmlpurifier_cache'));

		add_action('plugins_loaded', array($this, 'update_settings'));

		add_action('init', array($this, 'allow_origin'));

		// Loads React for admin pages.
		add_action('admin_enqueue_scripts', array($this, 'register_frontity_scripts'));
		add_action('rest_api_init', array($this, 'rest_routes'));
		add_action('registered_post_type', array($this, 'add_custom_post_types_filters'));

		add_action('wp_head', array($this, 'amp_add_canonical'));

		add_action('embed_footer', array($this, 'send_post_embed_height'));

		add_filter('wp_get_attachment_link', array($this, 'add_id_to_gallery_images'), 10, 2);
		add_filter('wp_get_attachment_image_attributes', array($this, 'add_id_to_gallery_image_attributes'), 10, 2);

		/** 
		 * Used to test plugin_updated_complete function.
		 * 
		 * add_action('wp_ajax_frontity_upgrade_plugin', array($this, 'upgrade_plugin'));
		 * 
		 **/
	}

	/**
		* 	Used to test the plugin_update_completed function.
		* 
		*	function upgrade_plugin() {
		*		$plugin = plugin_basename(__FILE__);
		*		$this->plugin_update_completed(null, array(
		*			'action' => 'update',
		*			'type' => 'plugin',
		*			'bulk' => 1,
		*			'plugins' => array($plugin)
		*		));
		*	} 
		*
		* */

	// Sets transient to check if plugin has been updated.
	function plugin_update_completed($upgrader_object, $data) {
		$our_plugin = plugin_basename(__FILE__);

		// Check if our plugin is being updated.
		if (
			$data['action'] === 'update' &&
			$data['type'] === 'plugin' &&
			isset($data['plugins'])
		) {
			foreach ($data['plugins'] as $plugin) {
				if ($plugin == $our_plugin) {
					set_transient('frontity_update', $this->plugin_version);
				}
			}
		}
	}

	// Updates settings if plugin has been updated.
	function update_settings() {
		$should_update = false;
		$frontity_update_transient = get_transient('frontity_update');

		if (!$frontity_update_transient) {
			$should_update = true;
		} else if ($frontity_update_transient !== $this->plugin_version) {
			$should_update = true;
		}

		if ($should_update && current_user_can('update_plugins')) {
			frontity_update_settings();
			set_transient('frontity_update', $this->plugin_version);
		}
	}

	// Updates settings in WP db after user input.
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

	// Add resizement to wp-embedded iframes.
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

	// Save the image id transient keys for future purges.
	function update_image_id_transient_keys($new_transient_key) {
		$transient_keys = get_option('image_id_transient_keys');
		$transient_keys[] = $new_transient_key;
		update_option('image_id_transient_keys', $transient_keys);
	}

	// Purge (delete) all the image id transients.
	function purge_image_id_transient_keys() {
		$transient_keys = get_option('image_id_transient_keys');
		foreach ($transient_keys as $t) {
			delete_transient($t);
		}
		update_option('image_id_transient_keys', array());
	}

	// Add data-attachment-ids to galleries using the wp_get_attachment_image_attributes hook.
	function add_id_to_gallery_image_attributes($attrs, $attachment) {
		$attrs['data-attachment-id'] = $attachment->ID;
		$attrs['data-attachment-id-source'] = 'image-attributes-hook';
		return $attrs;
	}

	// Add data-attachment-ids to galleries using the wp_get_attachment_link hook.	
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

	// Add hooks for each custom post type.
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

	// Add latest hook.
	function wp_api_get_latest($p) {
		$types = apply_filters('add_custom_post_types_to_latest', array($p['type']));
		return $types;
	}

	// Register our own routes.
	function rest_routes() {
		register_rest_route('frontity/v1', '/info/', array(
			'methods' => 'GET',
			'callback' => array($this, 'get_info'),
		));
		register_rest_route('frontity/v1', '/discover/', array(
			'methods' => 'GET',
			'callback' => array($this, 'discover_url')
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

	// Get plugin info from the database. Used in the REST API.
	function get_info() {
		$plugin = array(
			'version' => $this->plugin_version,
			'settings' => get_option("frontity_settings"),
		);

		$site = array(
			'locale' => get_locale(),
			'timezone' => get_option('timezone_string'),
			'gmt_offset' => intval(get_option('gmt_offset')),
			'per_page' => intval(get_option('posts_per_page')),
		);

		return array(
			'plugin' => $plugin,
			'site' => $site,
		);
	}

	// Get latest info of each custom post type.
	function get_latest_from_cpt($cpts) {
		$result = array();
		foreach ($cpts as &$cpt) {
			if (post_type_exists($cpt)) {
				$cpt_object = get_post_type_object($cpt);
				if ($cpt_object->show_in_rest) {
					if ($cpt === 'post' &&
						get_option('show_on_front') === 'page' &&
						get_option('frontity_settings')['frontpage_forced']) {
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

	// Return latest info on the individual endpoint.
	function latest_individual_endpoint($data) {
		$cpts = apply_filters(
			'add_custom_post_types_to_latest',
			array($cpt = $data->get_url_params()['id'])
		);
		return $this->get_latest_from_cpt($cpts);
	}

	// Return latest info on the general endpoint.
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

	// Add latest info in the _links section of each post.
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

	// Try to get the image id from the database and store it using transients.
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

	// If an image doesn't have permissions to be shown in the database, fix it.
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

	// Add data-attachment-id to content images.
	function add_image_ids($response, $post_type, $request)	{
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
			$this->fix_forbidden_media($response->data['featured_media']);

		$dom = new simple_html_dom();

		$dom->load(
			isset($response->data['content']['rendered']) ?
			$response->data['content']['rendered'] :
			""
		);

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
			$response->add_links(array(
				'wp:contentmedia' => array(
					'href' => $media_url,
					'embeddable' => true,
				)
			));
		}
		$html = $dom->save();
		if ($html) $response->data['content']['rendered'] = $html;
		$response->data['content_media'] = $imgIds;
		return $response;
	}

	// Use HTML Purifier in the content.
	function purify_html($response, $post_type, $request)	{
		$disableHtmlPurifier = $request->get_param('disableHtmlPurifier');
		$settings = get_option('frontity_settings');

		// Removes HTML tags from 'title.rendered' and
		// saves the result in a new field called 'text'.
		if (isset($response->data['title']['rendered'])) {
			$response->data['title']['text'] =
			strip_tags(html_entity_decode($response->data['title']['rendered']));
		}

		// Removes HTML tags from 'excerpt.rendered' and
		// saves the result in a new field called 'text'.
		if (isset($response->data['excerpt']['rendered'])) {
			$response->data['excerpt']['text'] =
			strip_tags(html_entity_decode($response->data['excerpt']['rendered']));
		}

		if ($disableHtmlPurifier === 'true' || !$settings['html_purifier_active']) {
			return $response;
		}

		require_once(plugin_dir_path(__FILE__) . '/libs/purifier.php');

		if (isset($response->data['content']['rendered'])) {
			$purifier = load_purifier();
			$purifiedContent = $purifier->purify($response->data['content']['rendered']);

			if (!empty($purifiedContent)) {
				$response->data['content']['rendered'] = $purifiedContent;
			}
		}

		return $response;
	}

	// Delete directory. Used when purging HTML Purifier files.
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

	// Purge the Html Purifier files.
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

	// Load React in admin pages.
	function register_frontity_scripts($hook) {
		if (
			'toplevel_page_frontity-dashboard' === $hook ||
			'frontity_page_frontity-settings' === $hook
		) {
			wp_register_script(
				'frontity_admin_js',
				plugin_dir_url(__FILE__) . 'admin/dist/main.js',
				array(),
				$this->plugin_version,
				true
			);
			wp_enqueue_script('frontity_admin_js');
		}
	}

	// Adds the admin pages to the menu.
	function render_frontity_admin() {
		$icon_url = trailingslashit(plugin_dir_url(__FILE__)) . "admin/assets/frontity_20x20.png";
		$position = 64.999989; //Right before the "Plugins"

		add_menu_page(
			'Frontity',
			'Frontity',
			'manage_options',
			'frontity-dashboard',
			function() {
				include('admin/index.php');
			},
			$icon_url,
			$position
		);

		add_submenu_page(
			'frontity-dashboard',
			'Dashboard',
			'Dashboard',
			'manage_options',
			'frontity-dashboard',
			function() {
				include('admin/index.php');
			}
		);

		add_submenu_page(
			'frontity-dashboard',
			'Advanced Settings',
			'Advanced Settings',
			'manage_options',
			'frontity-settings',
			function() {
				include('admin/index.php');
			}
		);
	}

	// Our first implementation of url discovery.
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

	// Adds Cross origin * to the header
	function allow_origin() {
		if (!headers_sent()) header("Access-Control-Allow-Origin: *");
	}

	// Injects the AMP URL to the header.
	public function amp_add_canonical() {
		$settings = get_option('frontity_settings');
		$prettyPermalinks = get_option('permalink_structure') !== '';
		$url = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST']
			. $_SERVER['REQUEST_URI'];
		$initialUrl = $prettyPermalinks ? strtok($url, '?') : $url;
		$amp_active = $settings['amp_active'];
		$amp_server = $settings['amp_server'];
		$ampForced = false;
		$dev = 'false';
		$excludes = isset($settings['excludes']) ? $settings['excludes'] : array();
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
			$amp_server = $_GET['ampUrl'];
			$dev = 'true';
		}
		if (isset($_GET['dev'])) $dev = $_GET['dev'];

				//posts
		if ($ampForced || (isset($amp_active) && ($amp_active) && (is_single()) && $exclusion === false)) {
			$id = get_queried_object()->ID;
			$type = get_queried_object()->post_type;
			$permalink = get_permalink($id);
			$path = parse_url($permalink, PHP_URL_PATH);
			$query = '?siteId=' . $settings["site_id"]
				. '&dev=' . $dev
				. '&type=' . $type
				. '&id=' . $id
				. '&initialUrl=' . $initialUrl;
			$amp_url = $amp_server . $path . $query;

			printf('<link rel="amphtml" href="%s" />', $amp_url);
			printf("\n");
		}
	}
}

// This initializates the frontity class and sets the global path.
function frontity() {
	global $frontity;

	if (!isset($frontity)) $frontity = new Frontity();

	require_once('includes/filter-fields.php');

	$GLOBALS['wp_pwa_path'] = '/' . basename(plugin_dir_path(__FILE__));
	$GLOBALS['wp_pwa_url'] = plugin_dir_url(__FILE__);

	return $frontity;
}

// Initialize frontity.
frontity();

// Initialize frontity_settings if they don't exist.
function frontity_initialize_settings() {
	$defaults = array(
		"site_id_requested" => false,
		"site_id" => "",
		"pwa_active" => false,
		"amp_active" => false,
		"ssr_server" => "https://ssr.wp-pwa.com",
		"static_server" => "https://static.wp-pwa.com",
		"amp_server" => "https://amp.wp-pwa.com",
		"frontpage_forced" => false,
		"html_purifier_active" => true,
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
}

function frontity_update_settings() {
	$settings = get_option('frontity_settings');
	$old_settings = get_option('wp_pwa_settings');

	// Initialize settings when the plugin is updated
	// but was already activated (update doesn't trigger activation hook).
	if (!$settings) {
		frontity_initialize_settings();
		$settings = get_option('frontity_settings');
	}

	// If there are settings from the previous versions of the plugin
	// map them into the new settings and delete the old settings.
	if ($old_settings) {
		if (isset($old_settings['wp_pwa_status'])) {
			$settings['pwa_active'] = $old_settings['wp_pwa_status'] == 'mobile' ? true : false;
		}
		if (isset($old_settings['wp_pwa_amp'])) {
			$settings['amp_active'] = $old_settings['wp_pwa_amp'] == 'posts' ? true : false;
		}
		if (isset($old_settings['wp_pwa_siteid'])) {
			$settings['site_id'] = $old_settings['wp_pwa_siteid'];
		}
		if (isset($old_settings['wp_pwa_ssr'])) {
			$settings['ssr_server'] = $old_settings['wp_pwa_ssr'];
		}
		if (isset($old_settings['wp_pwa_static'])) {
			$settings['static_server'] = $old_settings['wp_pwa_static'];
		}
		if (isset($old_settings['wp_pwa_amp_server'])) {
			$settings['amp_server'] = $old_settings['wp_pwa_amp_server'];
		}
		if (isset($old_settings['wp_pwa_force_frontpage'])) {
			$settings['frontpage_forced'] = $old_settings['wp_pwa_force_frontpage'];
		}
		if (isset($old_settings['wp_pwa_excludes'])) {
			$settings['excludes'] = $old_settings['wp_pwa_excludes'];
		}
		if (isset($old_settings['wp_pwa_api_fields'])) {
			$settings['api_filters'] = $old_settings['wp_pwa_api_fields'];
		}

		update_option('frontity_settings', $settings);
		delete_option('wp_pwa_settings');
	}
}

function frontity_activation() {
	frontity_update_settings();
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
	delete_transient('frontity_update');
}

function frontity_uninstallation() {
	delete_option('frontity_settings');
}

register_activation_hook(__FILE__, 'frontity_activation');
register_deactivation_hook(__FILE__, 'frontity_deactivation');
register_uninstall_hook( __FILE__, 'frontity_uninstallation' );

endif; // class_exists check
