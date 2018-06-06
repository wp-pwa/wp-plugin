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
?>
var siteId = "<?php echo$settings['wp_pwa_siteid']; ?>";
var dynamicUrl = "https://007764b3.ngrok.io";
var staticUrl = "https://007764b3.ngrok.io";
importScripts('https://cdn.onesignal.com/sdks/OneSignalSDKWorker.js');
importScripts('https://007764b3.ngrok.io/static/sw.js');
