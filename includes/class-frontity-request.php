<?php

class Frontity_Request
{
  // Trinity.
  static protected $type;
  static protected $id;
  static protected $page;

  // Settings.
  static protected $pwa_active;
  static protected $amp_active;
  static protected $site_id;
  static protected $ssr_server;
  static protected $static_server;
  static protected $amp_server;
  static protected $excludes;
  static protected $excluded;
  static protected $per_page;
  static protected $initial_url;
  
  // Params.
  static protected $pwa_forced;
  static protected $amp_forced;
  static protected $debug_injector;

  function evaluate_trinity()
  {
    $settings = get_option('frontity_settings');
    $frontpage_forced = $settings['frontpage_forced'];
    $queried_object = get_queried_object();

    if (($frontpage_forced && is_front_page()) || is_home()) {
      self::$type = 'latest';
      self::$id = 'post';
      self::$page = 1;
    } else if (is_page() || is_single()) {
      if ($queried_object->post_type !== 'attachment') {
        self::$type = $queried_object->post_type;
        self::$id = $queried_object->ID;
      }
    } elseif (is_post_type_archive()) {
      if ((isset($queried_object->show_in_rest)) && ($queried_object->show_in_rest === true)) {
        self::$type = 'latest';
        self::$id = $queried_object->name;
        self::$page = 1;
      }
    } elseif (is_category()) {
      self::$type = 'category';
      self::$id = $queried_object->term_id;
      self::$page = 1;
    } elseif (is_tag()) {
      self::$type = 'tag';
      self::$id = $queried_object->term_id;
      self::$page = 1;
    } elseif (is_author()) {
      self::$type = 'author';
      self::$id = $queried_object->ID;
      self::$page = 1;
    }

    if (is_paged()) {
      if (is_front_page() && get_option('page_on_front') !== '0') {
        self::$page = get_query_var('page');
      } else {
        self::$page = get_query_var('paged');
      }
    }
  }

  function evaluate_settings()
  {
    $settings = get_option('frontity_settings');
    self::$pwa_active = $settings['pwa_active'];
    self::$amp_active = $settings['amp_active'];
    self::$site_id = !empty($_GET['siteId'])
      ? $_GET['siteId']
      : $settings['site_id'];
    self::$ssr_server = !empty($_GET['ssrUrl'])
      ? $GET['ssrUrl']
      : !empty($_GET['server'])
      ? $_GET['server']
      : $settings['ssr_server'];
    self::$static_server = !empty($_GET['staticUrl'])
      ? $GET['staticUrl']
      : !empty($_GET['server'])
      ? $_GET['server']
      : $settings['static_server'];
    self::$amp_server = !empty($_GET['ampUrl'])
      ? $_GET['ampUrl']
      : $settings['amp_server'];
    self::$excludes = $settings['excludes'];
    self::$excluded = array_reduce($settings['excludes'], function ($carry, $value) {
      $value = str_replace('/', '\/', $value);
      $url = (isset($_SERVER['HTTPS'])
        ? 'https'
        : 'http')
        . "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
      $result = !!preg_match("/{$value}/", $url);
      return $result || $carry;
    }, false);
    $url = (isset($_SERVER['HTTPS'])
      ? 'https'
      : 'http')
      . "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
    self::$per_page = get_option('posts_per_page');
    $permalink_structure = get_option('permalink_structure');
    $pretty_permalinks = !empty($permalink_structure);
    self::$initial_url = $pretty_permalinks
      ? strtok($url, '?')
      : $url;
    self::$pwa_forced = isset($_GET['pwa']);
    self::$amp_forced = isset($_GET['amp']);
    self::$debug_injector = isset($_GET['debugInjector']);
  }

  static function get($key)
  {
    return self::${"$key"};
  }
}