<?php

class Frontity_Rest_Api_Fields
{
  // Adds the `latest` field to every post_type.
  function add_latest_field_to_post_type($post_type)
  {
    register_rest_field(
      $post_type,
      'latest',
      array(
        'get_callback' => array($this, 'get_latest'),
      )
    );
  }
  
  // Returns the value for the `latest` field in post_type,
  // and sets a hook to filter it.
  function wp_api_get_latest($post_type)
  {
    return apply_filters('add_custom_post_types_to_latest', array($post_type['type']));
  }
}