<?php

class Frontity_Injector
{
  function __construct()
  {
    // Stores the header output in a buffer.
    add_action('get_header', array($this, 'store_header_output'));
		// Retrieves the header output from the buffer.
    add_action('wp_head', array($this, 'get_header_output'), 0);
    // Adds the injector to the header.
    add_filter('frontity_header_html', array($this, 'add_injector_to_header'));
  }

  static function to_string()
  {
    // Get needed settings.
    [
      'site_id' => $site_id,
      'ssr_server' => $ssr_server,
      'static_server' => $static_server,
      'excludes' => $excludes,
      'pwa_active' => $pwa_active,
      'frontpage_forced' => $frontpage_forced
    ] = get_option('frontity_settings');

    $type = null;
    $id = null;
    $page = null;

    $per_page = get_option('posts_per_page');
    $pretty_permalinks = get_option('permalink_structure') !== '';
    $url = (isset($_SERVER['HTTPS']) ? 'https' : 'http')
      . '://'
      . $_SERVER['HTTP_HOST']
      . $_SERVER['REQUEST_URI'];
    $initial_url = $pretty_permalinks ? strtok($url, '?') : $url;

    $wp_pwa = array(
      "site_id" => $site_id,
      "type" => $type,
      "id" => $id,
      "page" => $page,
      "per_page" => $per_page,
      "ssr_server" => $ssr_server,
      "static_server" => $static_server,
      "initial_url" => $initial_url,
      "excludes" => $excludes
    );

    $dev = false;

    $injector_file = FRONTITY_PATH
      . 'injector/'
      . ($dev ? 'injector.js' : 'injector.min.js');

    ob_start();
    include_once $injector_file;
    $injector = apply_filters('frontity_javascript_injector', ob_get_clean());

    return "<script type='text/javascript'>"
      . "window.frontity=" . json_encode($wp_pwa) . ";"
      . $injector
      . "</script>";
  }

  function store_header_output()
  {
    ob_start();
  }

  function get_header_output()
  {
    // String where the header content will be stored.
    $header_html = '';
    
    // Get the content from and clean every level of the buffer.
    while (ob_get_level()) {
      $header_html .= ob_get_clean();
    }
		
		// Apply any filters to the final output.
    echo apply_filters('frontity_header_html', $header_html);
  }

  function add_injector_to_header($html)
  {
    return preg_replace('/<head.*?>/', '$0' . self::to_string(), $html);
  }
}