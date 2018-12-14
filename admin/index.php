<?php
	$settings = get_option('frontity_settings');
?>

<div id='root'></div>
<script>
	window.frontity = {
		plugin: {
			settings: <?php echo $settings ? json_encode($settings) : json_encode(new stdClass()); ?>
		}
	};
</script>
