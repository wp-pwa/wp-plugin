<?php
/*
Plugin Name: Frontity
Plugin URI: https://wordpress.org/plugins/wp-pwa/
Description: WordPress plugin to turn WordPress blogs into Progressive Web Apps.
Version: 1.13.3
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
define('FRONTITY_VERSION', '1.13.3');
define('FRONTITY_PATH', plugin_dir_path(__FILE__));
define('FRONTITY_URL', plugin_dir_url(__FILE__));

// Checks if the current PHP version is supported.
function frontity_php_version_supported()
{
	$version_is_supported = version_compare(PHP_VERSION, '5.3', '>=');

	if (!$version_is_supported) {
		add_action('admin_notices', 'php_version_not_supported');

		function php_version_not_supported()
		{
			$class = 'notice notice-error';
			$message = 'Oops! You need PHP v5.3 or greater for Frontity to work.';

			printf('<div class="notice notice-error"><p>%1$s</p></div>', esc_html($message));
		}
	}

	return $version_is_supported;
}

// Checks if the current WP version is supported.
function frontity_wp_version_supported()
{
	$version = $GLOBALS['wp_version'];
	$version_is_supported = version_compare($version, '4.7', '>=');

	if (!$version_is_supported) {
		add_action('admin_notices', 'php_version_not_supported');

		function php_version_not_supported()
		{
			$class = 'notice notice-error';
			$message = 'Oops! You need WordPress v4.7 or greater for Frontity to work.';

			printf('<div class="notice notice-error"><p>%1$s</p></div>', esc_html($message));
		}
	}

	return $version_is_supported;
}

// Initializes Frontity.
function frontity()
{
	if (!frontity_php_version_supported() || !frontity_wp_version_supported()) return;

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
	if (!frontity_php_version_supported() || !frontity_wp_version_supported()) return;

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