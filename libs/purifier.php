<?php

function load_purifier() {
  require_once(plugin_dir_path(__FILE__) . '/htmlpurifier/library/HTMLPurifier.auto.php');
  require_once(plugin_dir_path(__FILE__) . '/htmlpurifier-html5/autoload.php');
  
  $config = HTMLPurifier_HTML5Config::createDefault();
  
  $upload = wp_upload_dir();
  $htmlpurifier_dir = $upload['basedir'] . DS . 'frontity' . DS . 'htmlpurifier';
  
  if (is_dir($htmlpurifier_dir)) {
    $config->set('Cache', 'SerializerPath', $htmlpurifier_dir);
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
  $config->set('HTML', 'ForbiddenElements', array('font', 'center', 'br'));

  // Rule: Remove empty elements.
  $config->set('AutoFormat', 'RemoveEmpty', true);
  $config->set('AutoFormat', 'RemoveEmpty.Predicate', array(
    'div' => array(0 => 'class'),
    'span' => array(0 => 'class'),
    'iframe' => array(0 => 'src'),
    'colgroup' => array(),
    'th' => array(),
    'td' => array(),
  ));
  $config->set('AutoFormat', 'RemoveEmpty.RemoveNbsp', true);
  $config->set('AutoFormat', 'AutoParagraph', true);

  // IMG Attributes
  // This is defined in HTML5Definitions.php line 108 (DON'T OVERWRITE IT!!)
  // $def->addAttribute('img', 'data-attachment-id', 'Text');

  return new HTMLPurifier($config);
}
