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
define('FRONTITY_VERSION', '1.13.0');
define('FRONTITY_PATH', plugin_dir_path(__FILE__));
define('FRONTITY_URL', plugin_dir_url(__FILE__));

// class Frontity
// {
// 	function __construct()
// 	{
// 		// Updates settings if plugin has been updated.
// 		add_action('init', array($this, 'allow_origin'));
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

// 	// Add data-attachment-id to content images.
// 	function add_image_ids($response, $post_type, $request)
// 	{

// 		if (!class_exists('simple_html_dom')) {
// 			require_once('libs/simple_html_dom.php');
// 		}
				
// 		// fix featured media if necessary
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