<?php
/*
Plugin Name: WordPress PWA
Plugin URI: https://wordpress.org/plugins/wp-pwa/
Description: WordPress plugin to turn WordPress blogs into Progressive Web Apps.
Version: 1.14.0
Author: WordPress PWA
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
define('FRONTITY_VERSION', '1.14.0');
define('FRONTITY_PATH', plugin_dir_path(__FILE__));
define('FRONTITY_URL', plugin_dir_url(__FILE__));

if (!class_exists('Frontity_Compatibility')) {
	require_once FRONTITY_PATH . 'includes/class-frontity-compatibility.php';
}

// Initializes Frontity.
function frontity()
{
	$frontity_compatibility = new Frontity_Compatibility();
	if ($frontity_compatibility->should_stop()) return;

	// This global should be removed in favor of the constant FRONTITY_PATH.
	$GLOBALS['wp_pwa_path'] = '/' . basename(plugin_dir_path(__FILE__));

	// Require Frontity main class.
	if (!class_exists('Frontity')) {
		require_once FRONTITY_PATH . 'includes/class-frontity.php';
	}

	if (!isset($frontity)) $frontity = new Frontity();

	$frontity->run();
}

function frontity_activation()
{
	$frontity_compatibility = new Frontity_Compatibility();
	if ($frontity_compatibility->should_stop()) return;

	if (!class_exists('Frontity_Settings')) {
		require_once FRONTITY_PATH . 'includes/class-frontity-settings.php';
	}
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
	$transient_keys = get_option('image_id_transient_keys');
	foreach ($transient_keys as $transient_key) {
		delete_transient($transient_key);
	}
	delete_option('image_id_transient_keys');
	delete_option('frontity_settings');
}

register_activation_hook(__FILE__, 'frontity_activation');
register_deactivation_hook(__FILE__, 'frontity_deactivation');
register_uninstall_hook(__FILE__, 'frontity_uninstallation');

frontity();