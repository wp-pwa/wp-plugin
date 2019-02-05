<?php

class Frontity_Filter_Fields
{
  protected $latest_filters;

  function __construct()
  {
    $this->latest_filters = array();
  }

  function add_post_type_filters($post_type)
  {
    add_filter('rest_prepare_' . $post_type, array($this, 'filter'), 20, 3);
  }

  function add_taxonomy_filters($taxonomy)
  {
    add_filter('rest_prepare_' . $taxonomy, array($this, 'filter'), 20, 3);
  }

  function filter($response, $post, $request)
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
