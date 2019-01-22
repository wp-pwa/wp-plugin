<?php
class Frontity_Filter_Fields
{
  public $latest_filters = array();

  public function __construct()
  {
    add_action('rest_api_init', array($this, 'init'), 20);
  }

  // Register the fields functionality for all posts.
  public function init()
  {
    // Get all public post types, default includes 'post','page','attachment' and custom types added before 'init'.
    $post_types = get_post_types(array('public' => true), 'objects');

    foreach ($post_types as $post_type) {
      // Test if this posttype should be shown in the rest api.
      $show_in_rest = (isset($post_type->show_in_rest) && $post_type->show_in_rest) ? true : false;

      if ($show_in_rest) {
        // We need the postname to enable the filter.
        $post_type_name = $post_type->name;

        // Add the filter. The api uses eg. 'rest_prepare_post' with 3 parameters.
        add_filter('rest_prepare_' . $post_type_name, array($this, 'filter'), 20, 3);
      }
    }

    $tax_types = get_taxonomies(array('public' => true), 'objects');

    foreach ($tax_types as $tax_type) {
      //Test if this taxonomy should be shown in the rest api.
      $show_in_rest = (isset($tax_type->show_in_rest) && $tax_type->show_in_rest) ? true : false;

      if ($show_in_rest) {
        // We need the postname to enable the filter.
        $tax_type_name = $tax_type->name;

        // Add the filter. The api uses eg. 'rest_prepare_category' with 3 parameters.
        add_filter('rest_prepare_' . $tax_type_name, array($this, 'filter'), 20, 3);
      }
    }

    // Also enable filtering 'categories', 'comments', 'taxonomies', 'terms' and 'users'.
    add_filter('rest_prepare_comment', array($this, 'filter'), 20, 3);
    add_filter('rest_prepare_taxonomy', array($this, 'filter'), 20, 3);
    add_filter('rest_prepare_term', array($this, 'filter'), 20, 3);
    add_filter('rest_prepare_category', array($this, 'filter'), 20, 3);
    add_filter('rest_prepare_user', array($this, 'filter'), 20, 3);
  }

  public function filter($response, $post, $request)
  {
    $embed = is_string($request->get_param('_embed'));
    $context = $request->get_param('context');
  
    // Checks if the request is after data to populate a WP_REST_Response.
    if ($context === 'view') {
      $filters = array_filter(explode(',', $request->get_param('excludeFields')));
      $this->latest_filters = $filters;
    // Checks if the request is after data to embed in a WP_REST_Response.
    } else if ($context === 'embed') {
      $filters = $this->latest_filters;
    }

    if (!empty($filters)) {
      // Get the original data with `_links` and, if requested, `_embedded`.
      $rest_server = rest_get_server();
      $data = $rest_server->response_to_data($response, $embed && in_array('_links', $filters));

      foreach ($filters as $filter) {
        $this->unset_array_path($data, $filter);
      }
      
      // Returns the filtered data if it's not empty.
      return rest_ensure_response($data);
    }

    return $response;
  }

  function unset_array_path(&$array, $path)
  {
    $path = explode('.', trim($path, '.'));
    $temp = &$array;

    foreach ($path as $key) {
      if (isset($temp[$key])) {
        if (!($key === end($path))) $temp = &$temp[$key];
      } else {
        return;
      }
    }

    unset($temp[end($path)]);
  }
}

new Frontity_Filter_Fields();
