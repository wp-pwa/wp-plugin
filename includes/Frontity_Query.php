<?php

class Frontity_Query
{
  static protected $type;
  static protected $id;
  static protected $page;

  function evaluate_query()
  {
    $frontpage_forced = get_option("frontity_settings")["frontpage_forced"];
    $queried_object = get_queried_object();

    if (($frontpage_forced && is_front_page()) || is_home()) {
      self::$type = "latest";
      self::$id = "post";
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

  static function get_type()
  {
    return self::$type;
  }

  static function get_id()
  {
    return self::$id;
  }

  static function get_page()
  {
    return self::$page;
  }
}