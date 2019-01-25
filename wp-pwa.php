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

// Define the directory seperator if it hasn't already.
if (!defined('DS')) {
	if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
		define('DS', '\\');
	} else {
		define('DS', '/');
	}
}

// Define Frontity constants.
define('FRONTITY_VERSION', '1.11.0');
define('FRONTITY_PATH', plugin_dir_path(__FILE__));
define('FRONTITY_URL', plugin_dir_url(__FILE__));

// class Frontity
// {
// 	function __construct()
// 	{
// 		// Purges HTMLPurifier cache.
// 		add_action('wp_ajax_frontity_purge_htmlpurifier_cache', array($this, 'purge_htmlpurifier_cache'));
// 		// Updates settings if plugin has been updated.
// 		add_action('init', array($this, 'allow_origin'));
// 		// Adds custom routes to REST API.
// 		add_action('rest_api_init', array($this, 'rest_routes'));
// 		// Adds filters for custom post types.
// 		add_action('registered_post_type', array($this, 'add_custom_post_types_filters'));
// 		add_action('embed_footer', array($this, 'send_post_embed_height'));
// 		add_filter('wp_get_attachment_link', array($this, 'add_id_to_gallery_images'), 10, 2);
// 		add_filter('wp_get_attachment_image_attributes', array($this, 'add_id_to_gallery_image_attributes'), 10, 2);
// 	}

// 	// Adds resizement to WP embedded posts.
// 	function send_post_embed_height()
// 	{
// 		echo "<script>"
// 			. "window.parent.postMessage({"
// 			. "sentinel:'amp',type:'embed-size',height:document.body.scrollHeight"
// 			. "},'*');"
// 			. "</script>";
// 	}

// 	// Save the image id transient keys for future purges.
// 	function update_image_id_transient_keys($new_transient_key)
// 	{
// 		$transient_keys = get_option('image_id_transient_keys');
// 		$transient_keys[] = $new_transient_key;
// 		update_option('image_id_transient_keys', $transient_keys);
// 	}

// 	// Purge (delete) all the image id transients.
// 	function purge_image_id_transient_keys()
// 	{
// 		$transient_keys = get_option('image_id_transient_keys');
// 		foreach ($transient_keys as $t) {
// 			delete_transient($t);
// 		}
// 		update_option('image_id_transient_keys', array());
// 	}

// 	// Add data-attachment-ids to galleries using the wp_get_attachment_image_attributes hook.
// 	function add_id_to_gallery_image_attributes($attrs, $attachment)
// 	{
// 		$attrs['data-attachment-id'] = $attachment->ID;
// 		$attrs['data-attachment-id-source'] = 'image-attributes-hook';
// 		return $attrs;
// 	}

// 	// Add data-attachment-ids to galleries using the wp_get_attachment_link hook.	
// 	function add_id_to_gallery_images($html, $attachment_id)
// 	{
// 		$attachment_id = intval($attachment_id);
// 		$html = str_replace(
// 			'<img ',
// 			sprintf(
// 				'<img data-attachment-id="%1$d" data-attachment-id-source="attachment-link-hook"',
// 				$attachment_id
// 			),
// 			$html
// 		);
// 		$html = apply_filters('jp_carousel_add_data_to_images', $html, $attachment_id);
// 		return $html;
// 	}

// 	// Add hooks for each custom post type.
// 	function add_custom_post_types_filters($post_type)
// 	{
// 		add_filter('rest_prepare_' . $post_type, array($this, 'purify_html'), 9, 3);
// 		add_filter('rest_prepare_' . $post_type, array($this, 'add_latest_to_links'), 10);
// 		add_filter('rest_prepare_' . $post_type, array($this, 'add_image_ids'), 10, 3);
// 		register_rest_field(
// 			$post_type,
// 			'latest',
// 			array(
// 				'get_callback' => array($this, 'wp_api_get_latest'),
// 				'schema' => null,
// 			)
// 		);
// 	}

// 	// Add latest hook.
// 	function wp_api_get_latest($p)
// 	{
// 		$types = apply_filters('add_custom_post_types_to_latest', array($p['type']));
// 		return $types;
// 	}

// 	// Register our own routes.
// 	function rest_routes()
// 	{
// 		register_rest_route('frontity/v1', '/info/', array(
// 			'methods' => 'GET',
// 			'callback' => array($this, 'get_info'),
// 		));
// 		register_rest_route('frontity/v1', '/discover/', array(
// 			'methods' => 'GET',
// 			'callback' => array($this, 'discover_url')
// 		));
// 		register_rest_route('wp/v2', '/latest/', array(
// 			'methods' => 'GET',
// 			'callback' => array($this, 'latest_general_endpoint')
// 		));
// 		register_rest_route('wp/v2', '/latest/(?P<id>\w+)', array(
// 			'methods' => 'GET',
// 			'callback' => array($this, 'latest_individual_endpoint'),
// 			'args' => array(
// 				'id' => array(
// 					'validate_callback' => function ($param) {
// 						return post_type_exists($param);
// 					}
// 				)
// 			)
// 		));
// 	}

// 	// Get plugin info from the database. Used in the REST API.
// 	function get_info()
// 	{
// 		$plugin = array(
// 			'version' => $this->plugin_version,
// 			'settings' => get_option("frontity_settings"),
// 		);

// 		$site = array(
// 			'locale' => get_locale(),
// 			'timezone' => get_option('timezone_string'),
// 			'gmt_offset' => intval(get_option('gmt_offset')),
// 			'per_page' => intval(get_option('posts_per_page')),
// 		);

// 		return array(
// 			'plugin' => $plugin,
// 			'site' => $site,
// 		);
// 	}

// 	// Get latest info of each custom post type.
// 	function get_latest_from_cpt($cpts)
// 	{
// 		$result = array();
// 		foreach ($cpts as &$cpt) {
// 			if (post_type_exists($cpt)) {
// 				$cpt_object = get_post_type_object($cpt);
// 				if ($cpt_object->show_in_rest) {
// 					if ($cpt === 'post' &&
// 						get_option('show_on_front') === 'page' &&
// 						get_option('frontity_settings')['frontpage_forced']) {
// 						$link = get_option('home');
// 					} else {
// 						$link = get_post_type_archive_link($cpt);
// 					}
// 					$data = array(
// 						"id" => $cpt,
// 						"link" => $link,
// 						"count" => intval(wp_count_posts($cpt)->publish),
// 						"name" => $cpt_object->label,
// 						"slug" => $cpt_object->name,
// 						"taxonomy" => 'latest'
// 					);
// 					if ($cpt === 'post') $data['name'] = get_bloginfo('name');
// 					$result[] = apply_filters('rest_prepare_latest', $data);
// 				}
// 			}
// 		}
// 		return $result;
// 	}

// 	// Return latest info on the individual endpoint.
// 	function latest_individual_endpoint($data)
// 	{
// 		$cpts = apply_filters(
// 			'add_custom_post_types_to_latest',
// 			array($cpt = $data->get_url_params()['id'])
// 		);
// 		return $this->get_latest_from_cpt($cpts);
// 	}

// 	// Return latest info on the general endpoint.
// 	function latest_general_endpoint($data)
// 	{
// 		$params = $data->get_params();
// 		foreach ($params as $params_cpt => $params_id) {
// 			if (post_type_exists($params_cpt)) {
// 				$cpt = $params_cpt;
// 			}
// 		}
// 		if (!isset($cpt)) {
// 			$cpts = apply_filters('add_custom_post_types_to_latest', get_post_types());
// 			return $this->get_latest_from_cpt($cpts);
// 		}
// 		$cpts = apply_filters('add_custom_post_types_to_latest', array($cpt));
// 		return $this->get_latest_from_cpt($cpts);
// 	}

// 	// Add latest info in the _links section of each post.
// 	function add_latest_to_links($data)
// 	{
// 		$type = $data->data['type'];
// 		$id = $data->data['id'];
// 		$terms_url = add_query_arg(
// 			$type,
// 			$id,
// 			rest_url('wp/v2/latest')
// 		);
// 		$data->add_links(array(
// 			'https://api.w.org/term' => array(
// 				'href' => $terms_url,
// 				'taxonomy' => 'latest',
// 				'embeddable' => true,
// 			)
// 		));
// 		return $data;
// 	}

// 	// Try to get the image id from the database and store it using transients.
// 	function get_attachment_id($url)
// 	{
// 		$transient_name = 'frt_' . md5($url);
// 		$attachment_id = get_transient($transient_name);
// 		$transient_miss = $attachment_id === false;

// 		if ($transient_miss) {
// 			$attachment_id = 0;
// 			$dir = wp_upload_dir();
// 			$uploadsPath = parse_url($dir['baseurl'])['path'];
// 			$isInUploadDirectory = strpos($url, $uploadsPath . '/') !== false;
// 			$wpHost = parse_url($dir['baseurl'])['host'];
// 			$isNotExternalDomain = strpos($url, $wpHost . '/') !== false;
// 			if ($isInUploadDirectory && $isNotExternalDomain) {
// 				$file = basename(urldecode($url));
// 				$query_args = array(
// 					'post_type' => 'attachment',
// 					'post_status' => 'inherit',
// 					'fields' => 'ids',
// 					'meta_query' => array(
// 						array(
// 							'value' => $file,
// 							'compare' => 'LIKE',
// 							'key' => '_wp_attachment_metadata',
// 						),
// 					)
// 				);
// 				$query = new WP_Query($query_args);
// 				if ($query->have_posts()) {
// 					foreach ($query->posts as $post_id) {
// 						$meta = wp_get_attachment_metadata($post_id);
// 						$original_file = basename($meta['file']);
// 						$cropped_image_files = wp_list_pluck($meta['sizes'], 'file');
// 						if ($original_file === $file || in_array($file, $cropped_image_files)) {
// 							$attachment_id = $post_id;
// 							break;
// 						}
// 					}
// 				}
// 			}

// 			set_transient($transient_name, $attachment_id, 0); // never expires
// 			$this->update_image_id_transient_keys($transient_name);
// 		}

// 		return array(
// 			'id' => intval($attachment_id),
// 			'miss' => $transient_miss,
// 		);
// 	}

// 	// If an image doesn't have permissions to be shown in the database, fix it.
// 	function fix_forbidden_media($id)
// 	{
// 		if (!$id) return;

// 		$id = (int)$id;
// 		$attachment = get_post($id);
// 		if ($attachment->post_type !== 'attachment') return;

// 		$parent = get_post($attachment->post_parent);
// 		if ($parent && $parent->post_status !== 'publish') {
// 			wp_update_post(
// 				array(
// 					'ID' => $id,
// 					'post_parent' => 0,
// 				)
// 			);
// 		}
// 	}

// 	// Add data-attachment-id to content images.
// 	function add_image_ids($response, $post_type, $request)
// 	{
// 		global $wpdb;

// 		$purge = $request->get_param('purgeContentMediaTransients') === 'true';
// 		$fixForbiddenMedia = $request->get_param('fixForbiddenMedia') === 'true';

// 		if (!class_exists('simple_html_dom')) {
// 			require_once('libs/simple_html_dom.php');
// 		}
				
// 				// remove image ids stored in transients if requested
// 		if ($purge) $this->purge_image_id_transient_keys();
				
// 				// fix featured media if necessary
// 		if ($fixForbiddenMedia)
// 			$this->fix_forbidden_media($response->data['featured_media']);

// 		$dom = new simple_html_dom();

// 		$dom->load(
// 			isset($response->data['content']['rendered']) ?
// 				$response->data['content']['rendered'] :
// 				""
// 		);

// 		$imgIds = [];

// 		foreach ($dom->find('img') as $image) {
// 			$dataAttachmentId = $image->getAttribute('data-attachment-id');
// 			$class = $image->getAttribute('class');
// 			preg_match('/\bwp-image-(\d+)\b/', $class, $wpImage);
// 			if ($dataAttachmentId) {
// 				$imgIds[] = intval($dataAttachmentId);
// 			} elseif ($wpImage && isset($wpImage[1])) {
// 				$image->setAttribute('data-attachment-id', $wpImage[1]);
// 				$image->setAttribute('data-attachment-id-source', 'wp-image-class');
// 				$imgIds[] = intval($wpImage[1]);
// 			} else {
// 				$result = $this->get_attachment_id($image->src);
// 				$id = $result['id'];
// 				$miss = $result['miss'];
// 				$image->setAttribute('data-attachment-id-source', 'wp-query-transient-' . ($miss ? 'miss' : 'hit'));
// 				if ($id !== 0) {
// 					$image->setAttribute('data-attachment-id', $id);
// 					$imgIds[] = intval($id);
// 				}
// 			}
// 		}
// 		if (sizeof($imgIds) > 0) {
// 			// Fix content media if necessary
// 			if ($fixForbiddenMedia)
// 				foreach ($imgIds as $imgId) $this->fix_forbidden_media($imgId);

// 			$media_url = add_query_arg(
// 				array(
// 					'include' => join(',', $imgIds),
// 					'per_page' => sizeof($imgIds),
// 				),
// 				rest_url('wp/v2/media')
// 			);
// 			$response->add_links(array(
// 				'wp:contentmedia' => array(
// 					'href' => $media_url,
// 					'embeddable' => true,
// 				)
// 			));
// 		}
// 		$html = $dom->save();
// 		if ($html) $response->data['content']['rendered'] = $html;
// 		$response->data['content_media'] = $imgIds;
// 		return $response;
// 	}

// 	// Use HTML Purifier in the content.
// 	function purify_html($response, $post_type, $request)
// 	{
// 		$disableHtmlPurifier = $request->get_param('disableHtmlPurifier');
// 		$settings = get_option('frontity_settings');

// 		// Removes HTML tags from 'title.rendered' and
// 		// saves the result in a new field called 'text'.
// 		if (isset($response->data['title']['rendered'])) {
// 			$response->data['title']['text'] =
// 				strip_tags(html_entity_decode($response->data['title']['rendered']));
// 		}

// 		// Removes HTML tags from 'excerpt.rendered' and
// 		// saves the result in a new field called 'text'.
// 		if (isset($response->data['excerpt']['rendered'])) {
// 			$response->data['excerpt']['text'] =
// 				strip_tags(html_entity_decode($response->data['excerpt']['rendered']));
// 		}

// 		if ($disableHtmlPurifier === 'true' || !$settings['html_purifier_active']) {
// 			return $response;
// 		}

// 		require_once(plugin_dir_path(__FILE__) . '/libs/purifier.php');

// 		if (isset($response->data['content']['rendered'])) {
// 			$purifier = load_purifier();
// 			$purifiedContent = $purifier->purify($response->data['content']['rendered']);

// 			if (!empty($purifiedContent)) {
// 				$response->data['content']['rendered'] = $purifiedContent;
// 			}
// 		}

// 		return $response;
// 	}

// 	// Delete directory. Used when purging HTML Purifier files.
// 	function rrmdir($dir)
// 	{
// 		if (is_dir($dir)) {
// 			$objects = scandir($dir);
// 			foreach ($objects as $object) {
// 				if ($object != "." && $object != "..") {
// 					if (filetype($dir . DS . $object) == "dir") {
// 						rrmdir($dir . DS . $object);
// 					} else {
// 						unlink($dir . DS . $object);
// 					}
// 				}
// 			}
// 			reset($objects);
// 			rmdir($dir);
// 		}
// 	}

// 	// Purge the Html Purifier files.
// 	function purge_htmlpurifier_cache()
// 	{
// 		$upload = wp_upload_dir();
// 		$upload_base = $upload['basedir'];
// 		$htmlpurifier_dir = $upload_base . DS . 'frontity' . DS . 'htmlpurifier';
// 		$this->rrmdir($htmlpurifier_dir . DS . 'HTML');
// 		$this->rrmdir($htmlpurifier_dir . DS . 'CSS');
// 		$this->rrmdir($htmlpurifier_dir . DS . 'URI');
// 		wp_send_json(array(
// 			'status' => 'ok',
// 		));
// 	}

// 	// Our first implementation of url discovery.
// 	function discover_url($request)
// 	{
// 		$first_folder = $request['first_folder'];
// 		$last_folder = $request['last_folder'];

// 		if (is_null($last_folder)) {
// 			return array('Error' => 'last_folder is missing');
// 		}

// 				// ----------------
// 				// Post
// 				// ----------------
// 		$args = array(
// 			'name' => $last_folder,
// 			'numberposts' => 1,
// 		);
// 		$post = get_posts($args);
// 		if (sizeof($post) > 0) {
// 			return $post[0];
// 		}

// 				// ----------------
// 				// Page
// 				// ----------------
// 		$args = array(
// 			'name' => $last_folder,
// 			'numberposts' => 1,
// 			'post_type' => 'page',
// 		);
// 		$page = get_posts($args);
// 		if (sizeof($page) > 0) {
// 			return $page[0];
// 		}

// 				// ----------------
// 				// Author
// 				// ----------------
// 		if ($first_folder === 'author') {
// 			$args = array(
// 				'author_name' => $last_folder,
// 			);
// 			$author = get_posts($args);
// 			if (sizeof($author) > 0) {
// 				return $author[0];
// 			} else {
// 				return (new stdClass()); //empty object instead of null
// 			}
// 		}

// 				// ----------------
// 				// Category
// 				// ----------------
// 		$category = get_term_by('slug', $last_folder, 'category');
// 		if ($category) {
// 			return $category;
// 		}

// 				// ----------------
// 				// Tag
// 				// ----------------
// 		$tag = get_term_by('slug', $last_folder, 'tag');
// 		if ($tag) {
// 			return $tag;
// 		}

// 				// ----------------
// 				// Custom Post type
// 				// ----------------

// 		$post_types = get_post_types('', 'object');
// 		$post_type = '';

// 		foreach ($post_types as $p) {
// 			if ($p->rewrite['slug'] == $first_folder) {
// 				$post_type = $p->name;
// 			}
// 		}

// 		if ($post_type !== '') {
// 			$args = array(
// 				'name' => $last_folder,
// 				'numberposts' => 1,
// 				'post_type' => $post_type,
// 			);
// 			$custom_post = get_posts($args);

// 			if (sizeof($custom_post) > 0) {
// 				return $custom_post[0];
// 			}
// 		}

// 				// ----------------
// 				// Custom Taxonomy
// 				// ----------------
// 		$taxonomies = get_taxonomies('', 'object');
// 		$taxonomy = '';

// 		foreach ($taxonomies as $t) {
// 			if ($t->rewrite['slug'] === $first_folder) {
// 				$taxonomy = $t->name;
// 			}
// 		}

// 		if ($taxonomy === '') {
// 			return array('Error' => $first_folder . ' not supported');
// 		}

// 		$custom_taxonomy = get_term_by('slug', $last_folder, $taxonomy);

// 		if ($custom_taxonomy) {
// 			return $custom_taxonomy;
// 		} else {
// 			return array('Error' => $first_folder . 'not supported');
// 		}

// 				// ----------------
// 				// first_folder not found
// 				// ----------------
// 		return array('Error' => $last_folder . ' not found');
// 	}

// 	// Adds Cross origin * to the header
// 	function allow_origin()
// 	{
// 		if (!headers_sent()) header("Access-Control-Allow-Origin: *");
// 	}
// }

function frontity_activation()
{
	require_once FRONTITY_PATH . 'includes/Frontity_Settings.php';
	$frontity_settings = new Frontity_Settings();
	$frontity_settings->keep_settings_updated();

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

	flush_rewrite_rules();
}

function frontity_deactivation()
{
	delete_transient('frontity_version');
}

function frontity_uninstallation()
{
	delete_option('frontity_settings');
}

register_activation_hook(__FILE__, 'frontity_activation');
register_deactivation_hook(__FILE__, 'frontity_deactivation');
register_uninstall_hook(__FILE__, 'frontity_uninstallation');


// Initializes Frontity.
function frontity()
{
	// This global should be removed in favor of the constant FRONTITY_PATH.
	$GLOBALS['wp_pwa_path'] = '/' . basename(plugin_dir_path(__FILE__));
 
	// Require Frontity main class.
	require_once FRONTITY_PATH . 'includes/Frontity.php';

	if (!isset($frontity)) $frontity = new Frontity();

	$frontity->run();
}

frontity();