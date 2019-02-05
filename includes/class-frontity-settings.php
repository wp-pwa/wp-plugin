<?php

class Frontity_Settings
{
  protected $default_settings;
  protected $should_initialize;
  protected $should_migrate;
  protected $should_update;

  function __construct()
  {
    $this->set_default_settings();
    $this->check_if_should_initialize();
    $this->check_if_should_migrate();
    $this->check_if_should_update();
  }

  private function set_default_settings()
  {
    $this->default_settings = array(
      'site_id_requested' => false,
      'site_id' => '',
      'pwa_active' => false,
      'amp_active' => false,
      'ssr_server' => 'https://ssr.wp-pwa.com',
      'static_server' => 'https://static.wp-pwa.com',
      'amp_server' => 'https://amp.wp-pwa.com',
      'frontpage_forced' => false,
      'html_purifier_active' => true,
      'excludes' => array(),
      'injection_type' => 'inline'
    );
  }

  private function check_if_should_initialize()
  {
    $settings = get_option('frontity_settings');
    $this->should_initialize = !$settings;
  }

  private function check_if_should_migrate()
  {
    $old_settings = get_option('wp_pwa_settings');
    $this->should_migrate = !!$old_settings;
  }

  private function check_if_should_update()
  {
    $transient_version = get_transient('frontity_version');
    $this->should_update = !$this->should_initialize && (!$transient_version || $transient_version !== FRONTITY_VERSION);
  }

  function initialize_settings()
  {
    update_option('frontity_settings', $this->default_settings);
    set_transient('frontity_version', FRONTITY_VERSION);
  }

  function migrate_settings()
  {
    $settings = get_option('frontity_settings');
    $old_settings = get_option('wp_pwa_settings');

    if (isset($old_settings['wp_pwa_status']))
      $settings['pwa_active'] = $old_settings['wp_pwa_status'] == 'mobile' ? true : false;
    if (isset($old_settings['wp_pwa_amp']))
      $settings['amp_active'] = $old_settings['wp_pwa_amp'] == 'posts' ? true : false;
    if (isset($old_settings['wp_pwa_siteid']))
      $settings['site_id'] = $old_settings['wp_pwa_siteid'];
    if (isset($old_settings['wp_pwa_ssr']))
      $settings['ssr_server'] = $old_settings['wp_pwa_ssr'];
    if (isset($old_settings['wp_pwa_static']))
      $settings['static_server'] = $old_settings['wp_pwa_static'];
    if (isset($old_settings['wp_pwa_amp_server']))
      $settings['amp_server'] = $old_settings['wp_pwa_amp_server'];
    if (isset($old_settings['wp_pwa_force_frontpage']))
      $settings['frontpage_forced'] = $old_settings['wp_pwa_force_frontpage'];
    if (isset($old_settings['wp_pwa_excludes']))
      $settings['excludes'] = $old_settings['wp_pwa_excludes'];

    update_option('frontity_settings', $settings);
    delete_option('wp_pwa_settings');
  }

  function update_settings()
  {
    $settings = get_option('frontity_settings');
    $defaults = $this->default_settings;
    
    // Remove deprecated settings.
    $valid_settings = array_intersect_key($settings, $defaults);
    // Replace defaults with existing settings.
    $defaults = array_replace($defaults, $valid_settings);

    update_option('frontity_settings', $defaults);
    set_transient('frontity_version', FRONTITY_VERSION);
  }

  function save_settings()
  {
    $data = json_decode(stripslashes($_POST["data"]), true);

    if ($data) update_option('frontity_settings', $data);

    wp_send_json($data);
  }

  function keep_settings_updated()
  {
    if ($this->should_initialize) {
      $this->initialize_settings();
    }

    if ($this->should_migrate) {
      $this->migrate_settings();
    }

    if ($this->should_update) {
      $this->update_settings();
    }
  }

  function get($key)
  {
    return $this->$key;
  }
}