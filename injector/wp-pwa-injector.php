<?php

// Copy on header.php, just after <head> the following code:
// if (isset($GLOBALS['wp_pwa_path'])) { require(WP_PLUGIN_DIR . $GLOBALS['wp_pwa_path'] .'/injector/wp-pwa-injector.php'); }

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

$site_info = array(
  'homepage_title' => get_bloginfo( 'name' ),
  'homepage_metadesc' => get_bloginfo( 'description' )
);

if(has_filter('wp_pwa_get_site_info')) {
  $site_info = apply_filters('wp_pwa_get_site_info', $site_info);
}

$homepage_title = $site_info['homepage_title'];
$homepage_metadesc = $site_info['homepage_metadesc'];

$settings = get_option('wp_pwa_settings');
if (isset($_GET['siteId'])) {
  $siteId = $_GET['siteId'];
} elseif (isset($settings['wp_pwa_siteid'])) {
  $siteId = $settings["wp_pwa_siteid"];
} else {
  $siteId = 'none';
}
if (isset($_GET['ssr'])) {
  $ssr = $_GET['ssr'];
} elseif (isset($_GET['server'])) {
  $ssr = $_GET['server'];
} elseif (isset($settings['wp_pwa_ssr'])) {
  $ssr = $settings["wp_pwa_ssr"];
} else {
  $ssr = 'https://ssr.wppwa.com';
}
if (isset($_GET['static'])) {
  $static = $_GET['static'];
} elseif (isset($_GET['server'])) {
  $static = $_GET['server'];
} elseif (isset($settings['wp_pwa_static'])) {
  $static = $settings["wp_pwa_static"];
} else {
  $static = 'https://static.wppwa.com';
}
$serverType = 'pre';
if ((isset($_GET['serverType']) && ($_GET['serverType'] === 'prod'))) {
  $serverType = 'prod';
}

?>

<script type='text/javascript'>
var siteId = '<?php echo $siteId; ?>', wpType = '<?php echo $wpType; ?>', wpId = '<?php echo $wpId; ?>', wpPage = '<?php echo $wpPage; ?>', ssr = '<?php echo $ssr; ?>', statik = '<?php echo $static; ?>', serverType = '<?php echo $serverType; ?>';
var homepageTitle = '<?php echo $homepage_title; ?>', homepageMetadesc = '<?php echo $homepage_metadesc; ?>';
<?php require(WP_PLUGIN_DIR . $GLOBALS['wp_pwa_path'] . '/injector/injector.js'); ?>
</script>
