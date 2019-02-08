<?php

class Frontity_Injector
{
  static protected $should_inject;
  static protected $injection_type;
  static protected $injector_string;

  function __construct()
  {
    $settings = get_option('frontity_settings');
    self::$injection_type = $settings['injection_type'];
  }

  // Evaluate if the injector should be injected.
  function check_if_should_inject()
  {
    $site_id = Frontity_Request::get('site_id');
    $type = Frontity_Request::get('type');
    $id = Frontity_Request::get('id');
    $page = Frontity_Request::get('page');
    $pwa_active = Frontity_Request::get('pwa_active');
    $pwa_forced = Frontity_Request::get('pwa_forced');
    $excluded = Frontity_Request::get('excluded');

    self::$should_inject = $site_id
      && $type
      && $id
      && (!isset($page) || $page <= 1)
      && ($pwa_forced || ($pwa_active && !$excluded));
  }

  // Generate the string with the script to be injected in
  // the header html and set a hook to filter the injector file.
  function generate_injector_string()
  {
    $type = Frontity_Request::get('type');
    $id = Frontity_Request::get('id');
    $page = Frontity_Request::get('page');
    $site_id = Frontity_Request::get('site_id');
    $ssr_server = Frontity_Request::get('ssr_server');
    $static_server = Frontity_Request::get('static_server');
    $excludes = Frontity_Request::get('excludes');
    $per_page = Frontity_Request::get('per_page');
    $initial_url = Frontity_Request::get('initial_url');
    $debug_injector = Frontity_Request::get('debug_injector');

    // Create the object to populate `window.frontity`.
    $window_frontity = stripslashes(json_encode(compact(
      'type',
      'id',
      'page',
      'site_id',
      'ssr_server',
      'static_server',
      'excludes',
      'per_page',
      'initial_url'
    )));

    // Get the injector file as a string and set a hook to filter it.
    if (self::$injection_type === 'inline') {
      ob_start();
      include_once FRONTITY_PATH
        . 'injector/'
        . ($debug_injector ? 'injector.js' : 'injector.min.js');
      $injector_file = apply_filters('frontity_injector_file', ob_get_clean());
    } else {
      $injector_url = FRONTITY_URL
        . 'injector/'
        . ($debug_injector ? 'injector.js' : 'injector.min.js')
        . '?ver=' . FRONTITY_VERSION;
    }

    // Save the injector string in the class property.
    self::$injector_string = '<script id="frontity-injector" type="text/javascript">'
      . 'window["wp-pwa"]=' . $window_frontity . ';'
      . ($debug_injector ? 'debugger;' : '')
      . (self::$injection_type === "inline"
      ? $injector_file . "</script>\n"
      : "</script>\n<script type=\"text/javascript\" src=\"{$injector_url}\"></script>\n");
  }

  // Start storing in the buffer the header html.
  function buffer_header_html()
  {
    ob_start();
  }

  // Get the header html from the buffer
  // and set a hook to filter the final header html.
  function render_header_html()
  {
    $header_html = '';

    while (ob_get_level()) {
      $header_html .= ob_get_clean();
    }

    echo apply_filters('frontity_header_html', $header_html);
  }

  // Inject the injector in the header html
  // if is not already injected.
  function inject_header_html($html)
  {
    if (self::$should_inject) {
      $is_already_injected = !!preg_match('/id="frontity-injector"/', $html);

      if (!$is_already_injected)
        return preg_replace('/<head.*?>/', '$0' . self::$injector_string, $html);
    }

    return $html;
  }

  static function get($key)
  {
    return self::${"$key"};
  }
}