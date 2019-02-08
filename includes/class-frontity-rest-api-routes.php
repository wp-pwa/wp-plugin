<?php

class Frontity_Rest_Api_Routes
{
  // Registers custom routes in /wp-json/frontity/v1/.
  function register_frontity_routes()
  {
    register_rest_route('frontity/v1', '/info', array(
      'methods' => 'GET',
      'callback' => array($this, 'info'),
    ));
    register_rest_route('frontity/v1', '/discover', array(
      'methods' => 'GET',
      'callback' => array($this, 'discover')
    ));
  }

  // Registers custom routes in /wp-json/wp/v2/.
  function register_wp_routes()
  {
    register_rest_route('wp/v2', '/latest', array(
      'methods' => 'GET',
      'callback' => array($this, 'latest_general_endpoint')
    ));
    register_rest_route('wp/v2', '/latest/(?P<id>\w+)', array(
      'methods' => 'GET',
      'callback' => array($this, 'latest_individual_endpoint'),
      'args' => array(
        'id' => array(
          'validate_callback' => function ($param) {
            return post_type_exists($param);
          }
        )
      )
    ));
  }

  // Returns the data for /wp-json/frontity/v1/info.
  function info()
  {
    $plugin = array(
      'version' => FRONTITY_VERSION,
      'settings' => get_option("frontity_settings"),
    );

    $site = array(
      'locale' => get_locale(),
      'timezone' => get_option('timezone_string'),
      'gmt_offset' => intval(get_option('gmt_offset')),
      'per_page' => intval(get_option('posts_per_page')),
    );

    return array(
      'plugin' => $plugin,
      'site' => $site,
    );
  }

  // Returns the data for /wp-json/frontity/v1/discover.
  function discover($request)
  {
    $first_folder = $request['first_folder'];
    $last_folder = $request['last_folder'];

    if (is_null($last_folder)) {
      return array('Error' => 'last_folder is missing');
    }

    // Post.
    $args = array(
      'name' => $last_folder,
      'numberposts' => 1,
    );
    $post = get_posts($args);
    if (sizeof($post) > 0) {
      return $post[0];
    }

    // Page.
    $args = array(
      'name' => $last_folder,
      'numberposts' => 1,
      'post_type' => 'page',
    );
    $page = get_posts($args);
    if (sizeof($page) > 0) {
      return $page[0];
    }

    // Author.
    if ($first_folder === 'author') {
      $args = array(
        'author_name' => $last_folder,
      );
      $author = get_posts($args);
      if (sizeof($author) > 0) {
        return $author[0];
      } else {
        return (new stdClass()); //empty object instead of null
      }
    }

    // Category.
    $category = get_term_by('slug', $last_folder, 'category');
    if ($category) {
      return $category;
    }

    // Tag.
    $tag = get_term_by('slug', $last_folder, 'tag');
    if ($tag) {
      return $tag;
    }

    // Custom Post Type.
    $post_types = get_post_types('', 'object');
    $post_type = '';

    foreach ($post_types as $p) {
      if ($p->rewrite['slug'] == $first_folder) {
        $post_type = $p->name;
      }
    }

    if ($post_type !== '') {
      $args = array(
        'name' => $last_folder,
        'numberposts' => 1,
        'post_type' => $post_type,
      );
      $custom_post = get_posts($args);

      if (sizeof($custom_post) > 0) {
        return $custom_post[0];
      }
    }

    // Custom Taxonomy.
    $taxonomies = get_taxonomies('', 'object');
    $taxonomy = '';

    foreach ($taxonomies as $t) {
      if ($t->rewrite['slug'] === $first_folder) {
        $taxonomy = $t->name;
      }
    }

    if ($taxonomy === '') {
      return array('Error' => $first_folder . ' not supported');
    }

    $custom_taxonomy = get_term_by('slug', $last_folder, $taxonomy);

    if ($custom_taxonomy) {
      return $custom_taxonomy;
    } else {
      return array('Error' => $first_folder . 'not supported');
    }

    // first_folder not found.
    return array('Error' => $last_folder . ' not found');
  }

  // Returns the data for /wp-json/wp/v2/latest.
  function latest_general_endpoint($request)
  {
    // Get the custom post types defined in the query.
    $params = array_keys($request->get_params());
    $custom_post_types = array_filter($params, function ($param) {
      return post_type_exists($param);
    });

    // Set a hook to filter the custom post types list.
    $custom_post_types = apply_filters(
      'add_custom_post_types_to_latest',
      !empty($custom_post_types) ? $custom_post_types : get_post_types()
    );

    return $this->get_latest_from_cpt($custom_post_types, $request);
  }

  // Return the data for /wp-json/wp/v2/latest/(?P<id>\w+).
  function latest_individual_endpoint($request)
  {
    // Get the custom post type defined in the path.
    $params = $request->get_url_params();
    $custom_post_type = $params['id'];

    // Set a hook to filter the custom post types list.
    $custom_post_type = apply_filters(
      'add_custom_post_types_to_latest',
      array($custom_post_type)
    );

    return $this->get_latest_from_cpt($custom_post_type, $request);
  }

  // Get latest info of each custom post type.
  private function get_latest_from_cpt($cpts, $request)
  {
    $result = array();
    foreach ($cpts as &$cpt) {
      if (post_type_exists($cpt)) {
        $cpt_object = get_post_type_object($cpt);
        if ($cpt_object->show_in_rest) {
          $settings = get_option('frontity_settings');
          if ($cpt === 'post'
            && get_option('show_on_front') === 'page'
            && $settings['frontpage_forced']) {
            $link = get_option('home');
          } else {
            $link = get_post_type_archive_link($cpt);
          }

          $latest = array(
            "id" => $cpt,
            "link" => $link,
            "count" => intval(wp_count_posts($cpt)->publish),
            "name" => ($cpt === 'post' ? get_bloginfo('name') : $cpt_object->label),
            "slug" => $cpt_object->name,
            "taxonomy" => 'latest'
          );

          $response = new WP_REST_Response($latest);
          $request->set_param('context', 'view');
          $result[] = apply_filters('rest_prepare_latest', $response, $latest, $request)->data;
        }
      }
    }

    return $result;
  }
}