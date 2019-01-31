<?php

class Frontity_Images
{
  protected $should_purge;
  protected $should_fix;

  function __construct()
  {
    $this->check_if_should_purge();
    $this->check_if_should_fix();

    var_dump($this->should_purge);
    var_dump($this - should_fix);
  }

  function check_if_should_purge()
  {
    $this->should_purge = isset($_GET['purgeContentMediaTransients']);
  }

  function check_if_should_fix()
  {
    $this->should_fix = isset($_GET['fixForbiddenMedia']);
  }



  function add_post_type_filters($post_type)
  {
    add_filter('rest_prepare_' . $post_type, array($this, 'add_image_ids'), 10, 3);
  }

  // Purge (delete) all the image id transients.
  function purge_content_media_transients()
  {
    if ($this->should_purge) {
      $transient_keys = get_option('image_id_transient_keys');
      foreach ($transient_keys as $transient_key) delete_transient($transient_key);
      update_option('image_id_transient_keys', array());
    }
  }

  // // If an image doesn't have permissions to be shown in the database, fix it.
  // function fix_forbidden_media($id = null)
  // {
  //   if ($this->should_fix && isset($id)) {
  //     $id = (int)$id;
  //     $attachment = get_post($id);
  //     if ($attachment->post_type !== 'attachment') return;

  //     $parent = get_post($attachment->post_parent);
  //     if ($parent && $parent->post_status !== 'publish') {
  //       wp_update_post(
  //         array(
  //           'ID' => $id,
  //           'post_parent' => 0,
  //         )
  //       );
  //     }
  //   }
  // }
}