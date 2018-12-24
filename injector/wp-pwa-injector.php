<?php

// Copy on header.php, just after <head> the following code:
// if (isset($GLOBALS['wp_pwa_path'])) { require(WP_PLUGIN_DIR . $GLOBALS['wp_pwa_path'] .'/injector/wp-pwa-injector.php'); }

$siteId = null;
$type = null;
$id = null;
$page = null;
$env = 'prod';
$perPage = get_option('posts_per_page');
$ssr = 'https://ssr.wp-pwa.com';
$static = 'https://static.wp-pwa.com';
$inject = false;
$pwa = false;
$exclusion = false;
$dev = 'false';
$break = false;
$prettyPermalinks = get_option('permalink_structure') !== '';
$url = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST']
  . $_SERVER['REQUEST_URI'];
$initialUrl = $prettyPermalinks ? strtok($url, '?') : $url;
$settings = get_option('frontity_settings');
$pwa_active = $settings['pwa_active'];
$forceFrontpage = $settings['frontpage_forced'];
$excludes = isset($settings['excludes']) ? $settings['excludes'] : array();

if (($forceFrontpage === true && is_front_page()) || is_home()) {
  $type = 'latest';
  $id = 'post';
  $page = 1;
} elseif (is_page() || is_single()) {
  if (get_queried_object()->post_type !== 'attachment') {
    $type = get_queried_object()->post_type;
    $id = get_queried_object()->ID;
  }
} elseif (is_post_type_archive()) {
  $queriedObject = get_queried_object();
  if ((isset($queriedObject->show_in_rest)) && ($queriedObject->show_in_rest === true)) {
    $type = 'latest';
    $id = $queriedObject->name;
    $page = 1;
  }
} elseif (is_category()) {
  $type = 'category';
  $id = get_queried_object()->term_id;
  $page = 1;
} elseif (is_tag()) {
  $type = 'tag';
  $id = get_queried_object()->term_id;
  $page = 1;
} elseif (is_author()) {
  $type = 'author';
  $id = get_queried_object()->ID;
  $page = 1;
}

if (is_paged()) {
  if (is_front_page() && get_option('page_on_front') !== '0') {
    $page = get_query_var('page');
  } else {
    $page = get_query_var('paged');
  }
}

if (isset($_GET['siteId'])) {
  $siteId = $_GET['siteId'];
} elseif (isset($settings['site_id']) && $settings['site_id'] !== '') {
  $siteId = $settings['site_id'];
}

if (isset($_GET['ssrUrl'])) {
  $ssr = $_GET['ssrUrl'];
} elseif (isset($_GET['server'])) {
  $ssr = $_GET['server'];
} elseif (isset($settings['ssr_server'])) {
  $ssr = $settings['ssr_server'];
}
if (isset($_GET['staticUrl'])) {
  $static = $_GET['staticUrl'];
} elseif (isset($_GET['server'])) {
  $static = $_GET['server'];
} elseif (isset($settings['static_server'])) {
  $static = $settings['static_server'];
}

if (isset($_GET['pwa']) && $_GET['pwa'] === 'true') {
  $pwa = true;
}

if (isset($_GET['pwa']) || isset($_GET['server']) || isset($_GET['staticUrl']) ||
  isset($_GET['ssrUrl']) || isset($_GET['env']) || isset($_GET['siteId'])) {
  $dev = 'true';
}
if (isset($_GET['dev'])) {
  $dev = $_GET['dev'];
}
if (isset($_GET['break']) && ($_GET['break'] === 'true')) {
  $break = true;
}

if (sizeof($excludes) !== 0 && $pwa === false) {
  foreach ($excludes as $regex) {
    $output = array();
    $regex = str_replace('/', '\/', $regex);
    preg_match('/' . $regex . '/', $url, $output);
    if (sizeof($output) > 0) {
      $exclusion = true;
    }
  }
}

if ($siteId && $type && $id) {
  if ($pwa || ($pwa_active === true && $exclusion === false)) {
    $inject = true;
  }
  if (isset($page) && $page >= 2) {
    $inject = false;
  }
}

?>

<?php if ($inject) { ?>
  <script type='text/javascript'>window['wp-pwa']={siteId:'<?php echo $siteId; ?>',type:'<?php echo $type; ?>',id:'<?php echo $id; ?>',<?php if ($page) echo 'page:\'' . $page . '\',' ?>env:'<?php echo $env; ?>',dev:'<?php echo $dev; ?>',perPage:'<?php echo $perPage; ?>',ssr:'<?php echo $ssr; ?>',initialUrl:'<?php echo $initialUrl; ?>',static:'<?php echo $static; ?>'<?php if ($break) echo ',break:true' ?><?php if (sizeof($excludes) !== 0) echo ',excludes:["' . str_replace('\\\\', '\\', implode('", "', $excludes)) . '"]' ?>};
  <?php if ($break) {
    echo 'debugger;';
    ob_start();
    include(WP_PLUGIN_DIR . $GLOBALS['wp_pwa_path'] . '/injector/injector.js');
    $buffer = ob_get_clean();
    $buffer = apply_filters('frontity_javascript_injector', $buffer);
    echo $buffer;
  } else {
    ob_start();
    include(WP_PLUGIN_DIR . $GLOBALS['wp_pwa_path'] . '/injector/injector.min.js');
    $buffer = ob_get_clean();
    $buffer = apply_filters('frontity_javascript_injector', $buffer);
    echo $buffer;
  } ?></script>
<?php 
} ?>
