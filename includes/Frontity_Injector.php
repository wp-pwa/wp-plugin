<?php

class Frontity_Injector
{
  static protected $should_inject;
  static protected $injector_string;

  function evaluate_request()
  {
    // Populate all the needed variables for the injector.
    $type = Frontity_Query::get_type();
    $id = Frontity_Query::get_id();
    $page = Frontity_Query::get_page();
    $settings = get_option('frontity_settings');
    $pwa_active = $settings['pwa_active'];
    $site_id = !empty($_GET['siteId'])
      ? $_GET['siteId']
      : $settings['site_id'];
    $ssr_server = !empty($_GET['ssrUrl'])
      ? $GET['ssrUrl']
      : !empty($_GET['server'])
      ? $_GET['server']
      : $settings['ssr_server'];
    $static_server = !empty($_GET['staticUrl'])
      ? $GET['staticUrl']
      : !empty($_GET['server'])
      ? $_GET['server']
      : $settings['static_server'];
    $excludes = $settings['excludes'];
    $protocol = isset($_SERVER['HTTPS'])
      ? 'https'
      : 'http';
    $url = $protocol . '://'
      . $_SERVER['HTTP_HOST']
      . $_SERVER['REQUEST_URI'];
    $pretty_permalinks = !empty(get_option('permalink_structure'));
    $initial_url = $pretty_permalinks
      ? strtok($url, '?')
      : $url;
    $per_page = get_option('posts_per_page');
    $pwa = isset($_GET['pwa']);
    $debug_injector = isset($_GET['debugInjector']);
    $exclusion = !empty(array_filter($excludes, function ($key, $value) {
      $value = str_replace('/', '\/', $value);
      return !!preg_match('/' . $value . '/', $url);
    }));

    // Store if the injector should be rendered.
    self::$should_inject = $site_id
      && $type
      && $id
      && (!isset($page) || $page <= 1)
      && ($pwa || ($pwa_active && !$exclusion));

    // Create the object to populate `window.frontity`.
    $window_frontity = compact(
      "type",
      "id",
      "page",
      "site_id",
      "ssr_server",
      "static_server",
      "excludes",
      "per_page",
      "initial_url"
    );

    
    // Get the injector file as a string and set a hook to filter it.
    ob_start();
    include_once FRONTITY_PATH
      . 'injector/'
      . ($debug_injector ? 'injector.js' : 'injector.min.js');
    $injector = apply_filters('frontity_injector_file', ob_get_clean());
 
    // Saves the injector string in the class property.
    self::$injector_string = "<script id='frontity-injector' type='text/javascript'>"
      . "window.frontity=" . stripslashes(json_encode($window_frontity)) . ";"
      . ($debug_injector ? "debugger;" : "")
      . $injector
      . "</script>";
  }

  function buffer_header_html()
  {
    // Start storing in the buffer the header html.
    ob_start();
  }

  function render_header_html()
  {
    $header_html = '';
    
    // Get the header html from the buffer.
    while (ob_get_level()) {
      $header_html .= ob_get_clean();
    }
		
		// Set a hook to filter the final header html.
    echo apply_filters('frontity_header_html', $header_html);
  }

  function inject_header_html($html)
  {
    if (self::$should_inject) {
      $is_already_injected = !!preg_match("/id='frontity-injector'/", $html);

      if (!$is_already_injected)
        return preg_replace('/<head.*?>/', '$0' . self::$injector_string, $html);
    }

    return $html;
  }

  static function should_inject()
  {
    return self::$should_inject;
  }

  static function get_injector_string()
  {
    return self::$injector_string;
  }
}