<?php

class Frontity
{
  protected $version;
  protected $loader;

  function __construct()
  {
    $this->version = FRONTITY_VERSION;

    $this->load_dependencies();
    $this->define_admin_hooks();
    $this->define_public_hooks();
  }

  private function load_dependencies()
  {
    require_once FRONTITY_PATH . 'includes/Frontity_Loader.php';
    require_once FRONTITY_PATH . 'includes/Frontity_Admin.php';
    require_once FRONTITY_PATH . 'includes/Frontity_Settings.php';
    require_once FRONTITY_PATH . 'includes/Frontity_Request.php';
    require_once FRONTITY_PATH . 'includes/Frontity_Injector.php';
    require_once FRONTITY_PATH . 'includes/Frontity_Amp.php';

    $this->loader = new Frontity_Loader();
  }

  // Sorted by firing order.
  private function define_admin_hooks()
  {
    $this->loader->add_action('upgrader_process_complete', $this, 'plugin_update_completed');

    $frontity_admin = new Frontity_Admin();
    $this->loader->add_action('admin_menu', $frontity_admin, 'register_menu');
    $this->loader->add_action('admin_enqueue_scripts', $frontity_admin, 'register_script');
    $this->loader->add_action('admin_notices', $frontity_admin, 'render_notices');

    $frontity_settings = new Frontity_Settings();
    $this->loader->add_action('wp_ajax_frontity_save_settings', $frontity_settings, 'save_settings');
    $this->loader->add_action('plugins_loaded', $frontity_settings, 'keep_settings_updated');
  }

  // Sorted by firing order.
  private function define_public_hooks()
  {
    $frontity_request = new Frontity_Request();
    $this->loader->add_action('wp', $frontity_request, 'evaluate_request', 0);

    $frontity_injector = new Frontity_Injector();
    $this->loader->add_action('wp', $frontity_injector, 'check_if_should_inject');
    $this->loader->add_action('wp', $frontity_injector, 'generate_injector_string');
    $this->loader->add_action('get_header', $frontity_injector, 'buffer_header_html');
    $this->loader->add_action('wp_head', $frontity_injector, 'render_header_html', 0);
    $this->loader->add_filter('frontity_header_html', $frontity_injector, 'inject_header_html');

    $frontity_amp = new Frontity_Amp();
    $this->loader->add_action('wp', $frontity_amp, 'check_if_should_inject');
    $this->loader->add_action('wp', $frontity_amp, 'generate_link_string');
    $this->loader->add_action('wp_head', $frontity_amp, 'inject_header_html');
  }

  function run()
  {
    $this->loader->run();
  }
}