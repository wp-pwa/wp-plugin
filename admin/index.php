<?php
$settings = get_option('frontity_settings');
$site_url = get_site_url();
$locale = get_locale();
?>

<div id='root'></div>
<script>
	window.frontity = {
		plugin: {
			site_url: <?php echo json_encode($site_url); ?> || "",
			settings: <?php echo $settings ? json_encode($settings) : json_encode(new stdClass()); ?>,
			locale: <?php echo json_encode($locale); ?>
		}
	};
</script>
