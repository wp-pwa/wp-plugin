<?php

// Copy on header.php, just after <head> the following code:
//
// require(WP_PLUGIN_DIR . '/wordpress-pwa/injector/wp-pwa-injector.php');
//
// The plugin folder name /wordpress-pwa/ is exposed in the global $wp_pwa_path


if (is_home()) {
  $wpType = 'latest';
  $wpId = 0;
} elseif (is_single()) {
  $wpType = 'p';
  $wpId = get_queried_object()->ID;
} elseif (is_page()) {
  $wpType = 'page_id';
  $wpId = get_queried_object()->ID;
} elseif (is_category()) {
  $wpType = 'cat';
  $wpId = get_queried_object()->term_id;
} elseif (is_tag()) {
  $wpType = 'tag';
  $wpId = get_queried_object()->term_id;
} elseif (is_author()) {
  $wpType = 'author';
  $wpId = get_queried_object()->ID;
} elseif (is_search()) {
  $wpType = 's';
  $wpId = get_query_var('s');
} elseif (is_attachment()) {
  $wpType = 'attachment_id';
  $wpId = get_queried_object()->ID;
} elseif (is_date()) {
  $wpType = 'date';
  $wpId = get_query_var('m');
  if ($wpId === '') {
    $year = get_query_var('year');
    $monthnum = str_pad(get_query_var('monthnum'), 2, '0', STR_PAD_LEFT);
    $wpId = $year . $monthnum;
  }
} else {
  $wpType = 'none';
}

if (is_paged()) {
  $wpPage = get_query_var('paged');
} elseif (is_home() || is_category() || is_tag() || is_author() || is_search() || is_date()) {
  $wpPage = 1;
}

$settings = get_option('wp_pwa_settings');
if (isset($_GET['siteId'])) {
  $siteId = $_GET['siteId'];
} elseif (isset($settings['wp_pwa_siteid'])) {
  $siteId = $settings["wp_pwa_siteid"];
} else {
  $siteId = 'none';
}
if (isset($_GET['server'])) {
  $ssr = $_GET['server'];
} elseif (isset($_GET['ssr'])) {
  $ssr = $_GET['ssr'];
} elseif (isset($settings['wp_pwa_ssr'])) {
  $ssr = $settings["wp_pwa_ssr"];
} else {
  $ssr = 'https://pwa.worona.io';
}
if (isset($_GET['server'])) {
  $static = $_GET['server'];
} elseif (isset($_GET['static'])) {
  $static = $_GET['static'];
} elseif (isset($settings['wp_pwa_static'])) {
  $static = $settings["wp_pwa_static"];
} else {
  $static = 'https://pwa-static.worona.io';
}

?>

<script type='text/javascript'>
var siteId = '<?php echo $siteId; ?>', wpType = '<?php echo $wpType; ?>', wpId = '<?php echo $wpId; ?>', wpPage = '<?php echo $wpPage; ?>', ssr = '<?php echo $ssr; ?>', statik = '<?php echo $static; ?>';
<?php require(WP_PLUGIN_DIR . '/wordpress-pwa/injector/injector.min.js'); ?>
</script>