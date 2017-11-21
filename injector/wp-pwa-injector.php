<?php

// Copy on header.php, just after <head> the following code:
// if (isset($GLOBALS['wp_pwa_path'])) { require(WP_PLUGIN_DIR . $GLOBALS['wp_pwa_path'] .'/injector/wp-pwa-injector.php'); }

$siteId = null;
$listType = null;
$listId = null;
$page = null;
$singleType = null;
$singleId = null;
$env = 'pre';
$perPage = get_option('posts_per_page');

if (is_home()) {
  $listType = 'latest';
} elseif (is_page() || is_single()) {
  $singleType = get_queried_object()->post_type;
  $singleId = get_queried_object()->ID;
} elseif (is_post_type_archive()) {
  $queriedObject = get_queried_object();
  if ((isset($queriedObject->show_in_rest)) && (isset($queriedObject->rest_base)) &&
  ($queriedObject->show_in_rest === true)) {
    $listType = 'latest';
    $singleType = $queriedObject->rest_base;
  }
} elseif (is_tax()) {
  $listType = 'taxonomy';
  // $listId = get_queried_object()->term_id;
} elseif (is_category()) {
  $listType = 'category';
  $listId = get_queried_object()->term_id;
} elseif (is_tag()) {
  $listType = 'tag';
  $listId = get_queried_object()->term_id;
} elseif (is_author()) {
  $listType = 'author';
  $listId = get_queried_object()->ID;
} elseif (is_search()) {
  $listType = 'search';
  $listId = get_query_var('s');
} elseif (is_date()) {
  $listType = 'date';
  $listId = get_query_var('m');
  if ($listId === '') {
    $year = get_query_var('year');
    $monthnum = str_pad(get_query_var('monthnum'), 2, '0', STR_PAD_LEFT);
    $listId = $year . $monthnum;
  }
}

if (is_paged()) {
  $page = get_query_var('paged');
} elseif (is_home() || is_category() || is_tag() || is_author() || is_search() || is_date()) {
  $page = 1;
}

$site_info = array(
  'homepage_title' => get_bloginfo('name'),
  'homepage_metadesc' => get_bloginfo('description')
);

if(has_filter('wp_pwa_get_site_info')) {
  $site_info = apply_filters('wp_pwa_get_site_info', $site_info);
}

$homeTitle = $site_info['homepage_title'];
$homeDescription = $site_info['homepage_metadesc'];

$settings = get_option('wp_pwa_settings');
if (isset($_GET['siteId'])) {
  $siteId = $_GET['siteId'];
} elseif (isset($settings['wp_pwa_siteid'])) {
  $siteId = $settings["wp_pwa_siteid"];
}

if (isset($_GET['ssr'])) {
  $ssr = $_GET['ssr'];
} elseif (isset($_GET['server'])) {
  $ssr = $_GET['server'];
} elseif (isset($settings['wp_pwa_ssr'])) {
  $ssr = $settings["wp_pwa_ssr"];
} else {
  $ssr = 'https://ssr.wp-pwa.com';
}
if (isset($_GET['static'])) {
  $static = $_GET['static'];
} elseif (isset($_GET['server'])) {
  $static = $_GET['server'];
} elseif (isset($settings['wp_pwa_static'])) {
  $static = $settings["wp_pwa_static"];
} else {
  $static = 'https://static.wp-pwa.com';
}

if ((isset($_GET['env']) && ($_GET['env'] === 'prod'))) {
  $env = 'prod';
}

?>

<?php if ($siteId && ($listType || $singleType)) { ?>
  <script type='text/javascript'>
  window.['wp-pwa'] = {
    siteId: '<?php echo $siteId; ?>',
    <?php if ($listType) echo 'listType: \'' . $listType . '\',' ?>
    <?php if ($listId) echo 'listId: \'' . $listId . '\',' ?>
    <?php if ($singleType) echo 'singleType: \'' . $singleType . '\',' ?>
    <?php if ($singleId) echo 'singleId: \'' . $singleId . '\',' ?>
  }
  <?php require(WP_PLUGIN_DIR . $GLOBALS['wp_pwa_path'] . '/injector/injector.js'); ?>
  </script>
<?php } ?>
