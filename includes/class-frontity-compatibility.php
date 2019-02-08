<?php

class Frontity_Compatibility
{
  protected $php_supported;
  protected $wp_supported;

  function __construct()
  {
    $this->check_php_version();
    $this->check_wp_version();
  }

  // Checks if PHP version is supported.
  private function check_php_version()
  {
    $is_supported = version_compare(PHP_VERSION, '5.3', '>=');

    if (!$is_supported) {
      add_action('admin_notices', array($this, 'print_php_error'));
    }

    $this->php_supported = $is_supported;
  }
  
  // Checks if WP version is supported.
  private function check_wp_version()
  {
    $version = $GLOBALS['wp_version'];
    $is_supported = version_compare($version, '4.7', '>=');

    if (!$is_supported) {
      add_action('admin_notices', array($this, 'print_wp_error'));
    }

    $this->wp_supported = $is_supported;
  }

  // Prints error for PHP version not supported.
  function print_php_error()
  {
    $message = esc_html('Oops! You need PHP v5.3 or greater for Frontity to work.');
    echo "<div class=\"notice notice-error\"><p>{$message}</p></div>";
  }

  // Prints error for WP version not supported.
  function print_wp_error()
  {
    $message = esc_html('Oops! You need WordPress v4.7 or greater for Frontity to work.');
    echo "<div class=\"notice notice-error\"><p>{$message}</p></div>";
  }

  // Checks if Frontity should run.
  function should_stop()
  {
    return !$this->php_supported || !$this->wp_supported;
  }
}