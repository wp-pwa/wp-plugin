<?php

class Frontity_Admin
{
  // Renders any error from settings.
  function render_notices()
  {
    settings_errors();
  }

  // Adds the admin pages to the menu.
  function register_menu()
  {

    $icon_url = FRONTITY_URL . 'admin/assets/frontity_20x20.png';
    $position = 64.999989; //Right before the "Plugins"

    add_menu_page(
      'Frontity',
      'Frontity',
      'manage_options',
      'frontity-dashboard',
      function () {
        include FRONTITY_PATH . 'admin/index.php';
      },
      $icon_url,
      $position
    );

    add_submenu_page(
      'frontity-dashboard',
      'Dashboard',
      'Dashboard',
      'manage_options',
      'frontity-dashboard',
      function () {
        include FRONTITY_PATH . 'admin/index.php';
      }
    );

    add_submenu_page(
      'frontity-dashboard',
      'Advanced Settings',
      'Advanced Settings',
      'manage_options',
      'frontity-settings',
      function () {
        include FRONTITY_PATH . 'admin/index.php';
      }
    );
  }

  // Load React in admin pages.
  function register_script($hook)
  {
    if ('toplevel_page_frontity-dashboard' === $hook ||
      'frontity_page_frontity-settings' === $hook) {
      wp_register_script(
        'frontity_admin_js',
        FRONTITY_URL . 'admin/dist/main.js',
        array(),
        FRONTITY_VERSION,
        true
      );
      wp_enqueue_script('frontity_admin_js');
    }
  }
}