<?php
	/**
	 * Note: This file is intended to be publicly accessible.
	 */

define('WP_USE_THEMES', false); // Don't load theme support functionality
require_once dirname(__FILE__) . '/../../../../wp-load.php';

header('Service-Worker-Allowed: /');
header('Content-Type: application/javascript');
header('Cache-Control: no-cache');
header('X-Robots-Tag: none');

$settings = get_option('wp_pwa_settings');
$isPageOnFront = get_option('page_on_front') !== '0';
$type = $isPageOnFront ? 'page' : 'latest';
$id = $isPageOnFront ? get_option('page_on_front') : 'post';
if (!$isPageOnFront) $page = 1;
$dynamicUrl = substr($settings['wp_pwa_ssr'], -1) === '/' ? $settings['wp_pwa_ssr'] : $settings['wp_pwa_ssr'] . '/';
$staticUrl = substr($settings['wp_pwa_static'], -1) === '/' ? $settings['wp_pwa_static'] : $settings['wp_pwa_static'] . '/';
$initialUrl = substr(site_url(), -1) === '/' ? site_url() : site_url() . '/';
?>

var preCacheFiles = [];

var siteId = "<?php echo $settings['wp_pwa_siteid']; ?>";
var dynamicUrl = "<?php echo $dynamicUrl; ?>";
var staticUrl = "<?php echo $staticUrl; ?>";
var perPage = <?php echo get_option('posts_per_page'); ?>;
var type = "<?php echo $type; ?>";
var id = "<?php echo $id; ?>";
var initialUrl = "<?php echo $initialUrl; ?>";
<?php if (isset($page)) echo 'var page = ' . $page . ';'; ?>

importScripts('https://cdn.onesignal.com/sdks/OneSignalSDKWorker.js');
importScripts('<?php echo $dynamicUrl; ?>static/sw.js');
