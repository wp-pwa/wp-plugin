<?php
$plugin_dir_url = FRONTITY_URL;
$locale = get_locale();
$settings = get_option('frontity_settings');
?>

<div id='root'></div>
<script>
	window.frontity = {
		plugin: {
			plugin_dir_url: <?php echo json_encode($plugin_dir_url ? $plugin_dir_url : ''); ?>,
			locale: <?php echo json_encode($locale ? $locale : ''); ?>,
			settings: <?php echo json_encode($settings ? $settings : new stdClass()); ?>,
		}
	};
</script>
