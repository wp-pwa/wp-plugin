<?php

class Frontity_Rest_Api_Fields
{
  function add_post_type_filters($post_type)
  {
    add_filter('rest_prepare_' . $post_type, array($this, 'add_title_text_field'), 9, 1);
    add_filter('rest_prepare_' . $post_type, array($this, 'add_excerpt_text_field'), 9, 1);
    add_filter('rest_prepare_' . $post_type, array($this, 'add_latest_to_links'), 10);
  }
  
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
  function get_latest($post_type)
  {
    return apply_filters('add_custom_post_types_to_latest', array($post_type['type']));
  }

  // Removes HTML tags from 'title.rendered' and
  // saves the result in a new field called 'text'
  // in every post_type.
  function add_title_text_field($response)
  {
    if (isset($response->data['title']['rendered']))
      $response->data['title']['text'] = strip_tags(html_entity_decode($response->data['title']['rendered']));

    return $response;
  }

  // Removes HTML tags from 'excerpt.rendered' and
  // saves the result in a new field called 'text'
  // in every post_type.
  function add_excerpt_text_field($response)
  {
    if (isset($response->data['excerpt']['rendered']))
      $response->data['excerpt']['text'] = strip_tags(html_entity_decode($response->data['excerpt']['rendered']));

    return $response;
  }

  // Adds `latest` field to _links.
  function add_latest_to_links($response)
  {
    $type = $response->data['type'];
    $id = $response->data['id'];
    $terms_url = add_query_arg(
      $type,
      $id,
      rest_url('wp/v2/latest')
    );

    $response->add_links(array(
      'https://api.w.org/term' => array(
        'href' => $terms_url,
        'taxonomy' => 'latest',
        'embeddable' => true,
      )
    ));

    return $response;
  }
}