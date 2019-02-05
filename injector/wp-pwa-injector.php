<?php

// Copy on header.php, just after <head> the following code:
// if (isset($GLOBALS['wp_pwa_path'])) { require(WP_PLUGIN_DIR . $GLOBALS['wp_pwa_path'] .'/injector/wp-pwa-injector.php'); }
if (!class_exists('Frontity_Injector')) {
  require_once FRONTITY_PATH . 'includes/class-frontity-injector.php';
}

if (Frontity_Injector::get('should_inject')) {
  echo Frontity_Injector::get('injector_string');
}
