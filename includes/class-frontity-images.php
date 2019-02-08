<?php

class Frontity_Images
{
  protected $should_purge;
  protected $should_fix;

  function __construct()
  {
    $this->check_if_should_purge();
    $this->check_if_should_fix();
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

  // Deletes all the image id transients.
  function purge_content_media_transients()
  {
    if ($this->should_purge) {
      $transient_keys = get_option('image_id_transient_keys');
      foreach ($transient_keys as $transient_key) {
        delete_transient($transient_key);
      }
      update_option('image_id_transient_keys', array());
    }
  }


  // Saves the image id transient key for future purges.
  function update_content_media_transients($transient_name, $attachment_id)
  {
    set_transient($transient_name, $attachment_id);
    $transient_keys = get_option('image_id_transient_keys');
    $transient_keys[] = $transient_name;
    update_option('image_id_transient_keys', $transient_keys);
  }

  // Adds data-attachment-id to content images.
  function add_image_ids($response, $post_type, $request)
  {
    // Fix featured media if needed.
    if ($this->should_fix) {
      $this->fix_forbidden_media($response->data['featured_media']);
    }

    if (!class_exists('simple_html_dom')) {
      require_once FRONTITY_PATH . 'libs/simple_html_dom.php';
    }

    $dom = new simple_html_dom();
    $content = isset($response->data['content']['rendered'])
      ? $response->data['content']['rendered']
      : "";
    $image_ids = array();

    $dom->load($content);

    // Adds data-attachment-id attribute to every image in the content.
    foreach ($dom->find('img') as $image) {
      $data_attachment_id = $image->getAttribute('data-attachment-id');
      $class = $image->getAttribute('class');
      preg_match('/\bwp-image-(\d+)\b/', $class, $wp_image);

      // Uses data_attachment_id value if it's already an attribute.
      if ($data_attachment_id) {
        $image_ids[] = intval($data_attachment_id);
      // Uses the id from "wp-image-$id" class if it does exist.
      } elseif ($wp_image && isset($wp_image[1])) {
        // Checks if the src of the image is local so the image exists in db.
        if (strpos($image->getAttribute('src'), site_url()) === 0) {
          $image->setAttribute('data-attachment-id', $wp_image[1]);
          $image->setAttribute('data-attachment-id-source', 'wp-image-class');
          $image_ids[] = intval($wp_image[1]);
        }
      // Uses the id retrieved from the db.
      } else {
        $result = $this->get_attachment_id($image->src);
        $id = $result['id'];
        $miss = $result['miss'];
        $image->setAttribute('data-attachment-id-source', 'wp-query-transient-' . ($miss ? 'miss' : 'hit'));

        if ($id) {
          $image->setAttribute('data-attachment-id', $id);
          $image_ids[] = intval($id);
        }
      }
    }

    $html = $dom->save();

    if (count($image_ids)) {
      // Fixes content media if needed.
      if ($this->should_fix) {
        foreach ($image_ids as $image_id) {
          $this->fix_forbidden_media($image_id);
        };
      }

      // Adds `wp:contentmedia` to links.
      $media_url = add_query_arg(
        array(
          'include' => join(',', $image_ids),
          'per_page' => sizeof($image_ids),
        ),
        rest_url('wp/v2/media')
      );
      $response->add_links(array(
        'wp:contentmedia' => array(
          'href' => $media_url,
          'embeddable' => true,
        )
      ));
    };

    // Uses the modified html to populate `content.rendered`.
    if (!empty($html)) {
      $response->data['content']['rendered'] = $html;
    }

    // Creates a new `content_media` field populated
    // with an array of image ids from content.
    $response->data['content_media'] = $image_ids;

    return $response;
  }

  // If an image doesn't have permissions to be shown in the database, fix it.
  function fix_forbidden_media($id = null)
  {
    if ($this->should_fix && isset($id)) {
      $id = (int)$id;
      $attachment = get_post($id);
      if ($attachment->post_type !== 'attachment') return;

      $parent = get_post($attachment->post_parent);
      if ($parent && $parent->post_status !== 'publish') {
        wp_update_post(
          array(
            'ID' => $id,
            'post_parent' => 0,
          )
        );
      }
    }
  }

  // Tries to get the image id from the database and store it using transients.
  function get_attachment_id($url)
  {
    $transient_name = 'frt_' . md5($url);
    $attachment_id = get_transient($transient_name);
    $transient_miss = $attachment_id === false;

    if ($transient_miss) {
      $attachment_id = 0;
      $dir = wp_upload_dir();
      $parsedUrl = parse_url($dir['baseurl']);
      $uploads_path = $parsedUrl['path'];
      $is_in_upload_directory = strpos($url, $uploads_path . '/') !== false;
      $wp_host = $parsedUrl['host'];
      $is_not_external_domain = strpos($url, $wp_host . '/') !== false;

      if ($is_in_upload_directory && $is_not_external_domain) {
        $file = basename(urldecode($url));
        $query_args = array(
          'post_type' => 'attachment',
          'fields' => 'ids',
          'meta_query' => array(
            array(
              'value' => $file,
              'compare' => 'LIKE',
              'key' => '_wp_attachment_metadata',
            ),
          )
        );
        $query = new WP_Query($query_args);

        if ($query->have_posts()) {
          foreach ($query->posts as $post_id) {
            $meta = wp_get_attachment_metadata($post_id);
            $original_file = basename($meta['file']);
            $cropped_image_files = wp_list_pluck($meta['sizes'], 'file');

            if ($original_file === $file || in_array($file, $cropped_image_files)) {
              $attachment_id = $post_id;
              break;
            }
          }
        }
      }

      $this->update_content_media_transients($transient_name, $attachment_id);
    }

    return array(
      'id' => intval($attachment_id),
      'miss' => $transient_miss,
    );
  }

  // Add data-attachment-ids to galleries using the wp_get_attachment_image_attributes hook.
  function add_id_to_gallery_image_attributes($attrs, $attachment)
  {
    $attrs['data-attachment-id'] = $attachment->ID;
    $attrs['data-attachment-id-source'] = 'image-attributes-hook';
    return $attrs;
  }

	// Add data-attachment-ids to galleries using the wp_get_attachment_link hook.
  function add_id_to_gallery_images($html, $attachment_id)
  {
    $attachment_id = intval($attachment_id);
    $html = str_replace(
      '<img ',
      sprintf(
        '<img data-attachment-id="%1$d" data-attachment-id-source="attachment-link-hook"',
        $attachment_id
      ),
      $html
    );

    return $html;
  }
}