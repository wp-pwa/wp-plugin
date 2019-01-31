<?php

class Frontity_Purifier
{
  protected $should_purify;

  function __construct()
  {
    $this->check_if_should_purify();
  }

  function check_if_should_purify()
  {
    $disable_html_purifier = isset($_GET['disableHtmlPurifier']);
    $html_purifier_active = get_option('frontity_settings')['html_purifier_active'];

    $this->should_purify = !$disable_html_purifier && $html_purifier_active;
  }

  // Adds `purify` as a filter to every post_type in the REST API.
  function add_post_type_filters($post_type)
  {
    add_filter('rest_prepare_' . $post_type, array($this, 'purify'), 9, 1);
  }

  // Filters content with HTMLPurifier in every post_type.
  function purify($response)
  {
    if (!$this->should_purify) return $response;

    require_once FRONTITY_PATH . '/libs/purifier.php';

    if (isset($response->data['content']['rendered'])) {
      $purifier = load_purifier();
      $purified_content = $purifier->purify($response->data['content']['rendered']);

      if (!empty($purified_content))
        $response->data['content']['rendered'] = $purified_content;
    }

    return $response;
  }

  // Purges HTMLPurifier files.
  function purge_cache()
  {
    $upload = wp_upload_dir();
    $upload_base = $upload['basedir'];
    $htmlpurifier_dir = $upload_base . DS . 'frontity' . DS . 'htmlpurifier';
    $this->rrmdir($htmlpurifier_dir . DS . 'HTML');
    $this->rrmdir($htmlpurifier_dir . DS . 'CSS');
    $this->rrmdir($htmlpurifier_dir . DS . 'URI');
    wp_send_json(array(
      'status' => 'ok',
    ));
  }

  // Deletes directory. Used when purging HTMLPurifier files.
  private function rrmdir($dir)
  {
    if (is_dir($dir)) {
      $objects = scandir($dir);
      foreach ($objects as $object) {
        if ($object != "." && $object != "..") {
          if (filetype($dir . DS . $object) == "dir") {
            rrmdir($dir . DS . $object);
          } else {
            unlink($dir . DS . $object);
          }
        }
      }
      reset($objects);
      rmdir($dir);
    }
  }
}