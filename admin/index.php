<?php
global $wp_pwa;

$settings = get_option('frontity_settings');
$current_user = wp_get_current_user();

/* step & progress */
$progress = 0;
$step = 0;
$wp_version = get_bloginfo('version');
$rest_api_installed = $wp_pwa->rest_api_installed;
$rest_api_active = $wp_pwa->rest_api_active;
$rest_api_compatible = true;

if (version_compare($wp_version, '4.7', '>=')) { //From WP 4.7, the REST API is already installed.
	$rest_api_installed = true;
	$rest_api_active = true;
}

if (version_compare($wp_version, '4.4', '<')) { //REST API Plugin is only compatible from WP 4.4 ahead
	$rest_api_compatible = false;
} else if (!$rest_api_installed) {
	$step = 1;
} else if ($rest_api_installed && !$rest_api_active) {
	$step = 2;
	$progress = 33;
} else if ($rest_api_installed && $rest_api_active && !$synced_with_wp_pwa) {
	$step = 3;
	$progress = 66;
} else if ($rest_api_installed && $rest_api_active && $synced_with_wp_pwa) {
	$step = 4;
	$progress = 100;
}
?>

<div id='root'></div>
<script>
	window.frontity = {
		plugin: {
			settings: <?php echo $settings ? json_encode($settings) : json_encode(new stdClass()); ?>
		}
	};
</script>
