<?php

class Frontity_Purifier
{
  protected $should_purify;
  protected $purifier;

  function __construct()
  {
    $this->check_if_should_purify();
  }

  function check_if_should_purify()
  {
    $disable_html_purifier = isset($_GET['disableHtmlPurifier']);
    $settings = get_option('frontity_settings');
    $html_purifier_active = $settings['html_purifier_active'];

    $this->should_purify = !$disable_html_purifier && $html_purifier_active;
  }

  // Adds `purify` as a filter to every post_type in the REST API.
  function add_post_type_filters($post_type)
  {
    add_filter('rest_prepare_' . $post_type, array($this, 'purify'), 9, 1);
  }

  // Loads purifier config.
  function load_purifier()
  {
    if (!class_exists('HTMLPurifier_HTML5Config')) {
      require_once FRONTITY_PATH . 'libs/htmlpurifier/library/HTMLPurifier.auto.php';
      require_once FRONTITY_PATH . 'libs/htmlpurifier-html5/autoload.php';
    }

    $config = HTMLPurifier_HTML5Config::createDefault();

    $upload = wp_upload_dir();
    $htmlpurifier_dir = $upload['basedir'] . DS . 'frontity' . DS . 'htmlpurifier';

    if (is_dir($htmlpurifier_dir)) {
      $config->set('Cache.SerializerPath', $htmlpurifier_dir);
    } else {
      $config->set('Core', 'DefinitionCache', null);
    }

    $config->set('HTML.Doctype', 'HTML 4.01 Transitional');
    // Rule: All the CSS rules allowed.
    $config->set('CSS.AllowTricky', true);
    // Rule: IDs in elements allowed.
    $config->set('Attr.EnableID', true);
    // Rule: Don't resize big images.
    $config->set('CSS.MaxImgLength', null);
    $config->set('HTML.MaxImgLength', null);
    // Rule: All <iframe>s allowed.
    $config->set('HTML.SafeIframe', true);
    $config->set('URI.SafeIframeRegexp', '/.+/');
    // Rule: <font>, <center> and <br> not allowed.
    $config->set('HTML.ForbiddenElements', array('font', 'center', 'br'));
    // Rule: Remove empty elements.
    $config->set('AutoFormat.RemoveEmpty', true);
    $config->set('AutoFormat.RemoveEmpty.Predicate', array(
      'div' => array(0 => 'class'),
      'span' => array(0 => 'class'),
      'iframe' => array(0 => 'src'),
      'colgroup' => array(),
      'th' => array(),
      'td' => array(),
    ));
    $config->set('AutoFormat.RemoveEmpty.RemoveNbsp', true);
    $config->set('AutoFormat.AutoParagraph', true);
  
    // IMG Attributes
    // This is defined in HTML5Definitions.php line 108 (DON'T OVERWRITE IT!!)
    // $def->addAttribute('img', 'data-attachment-id', 'Text');

    $this->purifier = new HTMLPurifier($config);
  }

  // Filters content with HTMLPurifier in every post_type.
  function purify($response)
  {
    if (!$this->should_purify) return $response;

    if (!$this->purifier) $this->load_purifier();

    if (isset($response->data['content']['rendered'])) {
      $purified_content = $this->purifier->purify($response->data['content']['rendered']);

      if (!empty($purified_content)) {
        $response->data['content']['rendered'] = $purified_content;
      }
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