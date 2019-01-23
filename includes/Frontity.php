<?php

class Frontity
{
  protected $plugin_name;
  protected $version;
  protected $loader;

  function __construct()
  {
    $this->version = FRONTITY_VERSION;
    $this->plugin_name = "Frontity";

    $this->load_dependencies();
    $this->define_hooks();
  }

  private function load_dependencies()
  {
    require_once FRONTITY_PATH . "includes/Frontity_Loader.php";
    require_once FRONTITY_PATH . "includes/Frontity_Admin.php";
    require_once FRONTITY_PATH . "includes/Frontity_Query.php";
    require_once FRONTITY_PATH . "includes/Frontity_Injector.php";

    $this->loader = new Frontity_Loader();
  }

  private function define_hooks()
  {
    $frontity_admin = new Frontity_Admin();
    $this->loader->add_action("admin_notices", $frontity_admin, "render_notices");
    $this->loader->add_action("admin_menu", $frontity_admin, "register_menu");
    $this->loader->add_action("admin_enqueue_scripts", $frontity_admin, "register_script");

    $frontity_query = new Frontity_Query();
    $this->loader->add_action("wp", $frontity_query, "evaluate_query");

    $frontity_injector = new Frontity_Injector();
    $this->loader->add_action("wp", $frontity_injector, "evaluate_request");
    $this->loader->add_action("get_header", $frontity_injector, "buffer_header_html");
    $this->loader->add_action("wp_head", $frontity_injector, "render_header_html", 0);
    $this->loader->add_filter("frontity_header_html", $frontity_injector, "inject_header_html");
  }

  function get_plugin_name()
  {
    return $this->plugin_name;
  }

  function get_version()
  {
    return $this->version;
  }

  function get_loader()
  {
    return $this->loader;
  }

  function run()
  {
    $this->loader->run();
  }
}