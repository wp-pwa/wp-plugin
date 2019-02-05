<?php

class Frontity
{
  protected $loader;

  function __construct()
  {
    $this->load_dependencies();
    $this->define_admin_hooks();
    $this->define_public_hooks();
  }

  private function load_dependencies()
  {
    if (!class_exists('Frontity_Loader')) {
      require_once FRONTITY_PATH . 'includes/class-frontity-loader.php';
    }
    if (!class_exists('Frontity_Admin')) {
      require_once FRONTITY_PATH . 'includes/class-frontity-admin.php';
    }
    if (!class_exists('Frontity_Settings')) {
      require_once FRONTITY_PATH . 'includes/class-frontity-settings.php';
    }
    if (!class_exists('Frontity_Request')) {
      require_once FRONTITY_PATH . 'includes/class-frontity-request.php';
    }
    if (!class_exists('Frontity_Injector')) {
      require_once FRONTITY_PATH . 'includes/class-frontity-injector.php';
    }
    if (!class_exists('Frontity_Amp')) {
      require_once FRONTITY_PATH . 'includes/class-frontity-amp.php';
    }
    if (!class_exists('Frontity_Rest_Api_Routes')) {
      require_once FRONTITY_PATH . 'includes/class-frontity-rest-api-routes.php';
    }
    if (!class_exists('Frontity_Rest_Api_Fields')) {
      require_once FRONTITY_PATH . 'includes/class-frontity-rest-api-fields.php';
    }
    if (!class_exists('Frontity_Filter_Fields')) {
      require_once FRONTITY_PATH . 'includes/class-frontity-filter-fields.php';
    }
    if (!class_exists('Frontity_Purifier')) {
      require_once FRONTITY_PATH . 'includes/class-frontity-purifier.php';
    }
    if (!class_exists('Frontity_Images')) {
      require_once FRONTITY_PATH . 'includes/class-frontity-images.php';
    }
    if (!class_exists('Frontity_Miscellanea')) {
      require_once FRONTITY_PATH . 'includes/class-frontity-miscellanea.php';
    }

    $this->loader = new Frontity_Loader();
  }

  // Sorted by firing order.
  private function define_admin_hooks()
  {
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
    $this->loader->add_action('wp', $frontity_request, 'evaluate_trinity', 0);
    $this->loader->add_action('wp', $frontity_request, 'evaluate_settings', 0);

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

    $frontity_rest_api_routes = new Frontity_Rest_Api_Routes();
    $this->loader->add_action('rest_api_init', $frontity_rest_api_routes, 'register_frontity_routes');
    $this->loader->add_action('rest_api_init', $frontity_rest_api_routes, 'register_wp_routes');

    $frontity_rest_api_fields = new Frontity_Rest_Api_Fields();
    $this->loader->add_action('registered_post_type', $frontity_rest_api_fields, 'add_latest_field_to_post_type');
    $this->loader->add_action('registered_post_type', $frontity_rest_api_fields, 'add_post_type_filters');

    $frontity_filter_fields = new Frontity_Filter_Fields();
    $this->loader->add_action('registered_post_type', $frontity_filter_fields, 'add_post_type_filters');
    $this->loader->add_action('registered_taxonomy', $frontity_filter_fields, 'add_taxonomy_filters');
    $this->loader->add_filter('rest_prepare_comment', $frontity_filter_fields, 'filter', 20, 3);
    $this->loader->add_filter('rest_prepare_taxonomy', $frontity_filter_fields, 'filter', 20, 3);
    $this->loader->add_filter('rest_prepare_user', $frontity_filter_fields, 'filter', 20, 3);
    $this->loader->add_filter('rest_prepare_latest', $frontity_filter_fields, 'filter', 20, 3);

    $frontity_purifier = new Frontity_Purifier();
    $this->loader->add_action('registered_post_type', $frontity_purifier, 'add_post_type_filters');
    $this->loader->add_action('wp_ajax_frontity_purge_htmlpurifier_cache', $frontity_purifier, 'purge_cache');

    $frontity_images = new Frontity_Images();
    $this->loader->add_action('registered_post_type', $frontity_images, 'add_post_type_filters');
    $this->loader->add_action('plugins_loaded', $frontity_images, 'purge_content_media_transients');
    $this->loader->add_filter('wp_get_attachment_image_attributes', $frontity_images, 'add_id_to_gallery_image_attributes', 10, 2);
    $this->loader->add_filter('wp_get_attachment_link', $frontity_images, 'add_id_to_gallery_images', 10, 2);

    $frontity_miscellanea = new Frontity_Miscellanea();
    $this->loader->add_action('rest_pre_serve_request', $frontity_miscellanea, 'allow_origin');
    $this->loader->add_action('embed_footer', $frontity_miscellanea, 'send_post_embed_height');
  }

  function run()
  {
    $this->loader->run();
  }
}