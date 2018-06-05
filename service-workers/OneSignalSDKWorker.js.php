<?php
	/**
	 * Note: This file is intended to be publicly accessible.
	 */

define('WP_USE_THEMES', false); // Don't load theme support functionality
require_once dirname(__FILE__) . '/../../../../wp-load.php';

header('Service-Worker-Allowed: /');
header('Content-Type: application/javascript');
header('X-Robots-Tag: none');

$settings = get_option('wp_pwa_settings');
$siteId = $settings['wp_pwa_siteid'];
?>
const siteId = "<?php echo $siteId; ?>";
importScripts('https://cdn.onesignal.com/sdks/OneSignalSDKWorker.js');
