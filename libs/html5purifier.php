<?php

function load_html5purifier() {
  require_once(plugin_dir_path(__FILE__) . '/htmlpurifier/library/HTMLPurifier.auto.php');

  $config = HTMLPurifier_Config::createDefault();

  $upload = wp_upload_dir();
  $htmlpurifier_dir = $upload['basedir'] . DS . 'frontity' . DS . 'htmlpurifier';

  if (is_dir($htmlpurifier_dir)) {
    $config->set('Cache', 'SerializerPath', $htmlpurifier_dir);
	} else {
    $config->set('Core', 'DefinitionCache', null);
  }
  $config->set('HTML.Doctype', 'HTML 4.01 Transitional');

  $config->set('CSS.AllowTricky', true);
  $config->set('HTML.EnableAttrID', true);

  // Don't resize big images
  $config->set('CSS.MaxImgLength', null);
  $config->set('HTML.MaxImgLength', null);

  // Rule: <iframe> allowed.
  $config->set('HTML.SafeIframe', true);
  $config->set('URI.SafeIframeRegexp', '/.+/');

  // Rule: <center> not allowed.
  $config->set('HTML', 'ForbiddenElements', array('font', 'center', 'br'));

	// Rule: remove empty elements.
	$config->set('AutoFormat', 'RemoveEmpty', true);
  $config->set('AutoFormat', 'RemoveEmpty.RemoveNbsp', true);
  $config->set('AutoFormat', 'AutoParagraph', true);

  // Set some HTML5 properties
  $config->set('HTML.DefinitionID', 'html5-definitions'); // unique id
  $config->set('HTML.DefinitionRev', 1);

  if ($def = $config->maybeGetRawHTMLDefinition()) {
    // http://developers.whatwg.org/sections.html
    $def->addElement('section', 'Block', 'Flow', 'Common');
    $def->addElement('nav',     'Block', 'Flow', 'Common');
    $def->addElement('article', 'Block', 'Flow', 'Common');
    $def->addElement('aside',   'Block', 'Flow', 'Common');
    $def->addElement('header',  'Block', 'Flow', 'Common');
    $def->addElement('footer',  'Block', 'Flow', 'Common');
    // Content model actually excludes several tags, not modelled here
    $def->addElement('address', 'Block', 'Flow', 'Common');
    $def->addElement('hgroup', 'Block', 'Required: h1 | h2 | h3 | h4 | h5 | h6', 'Common');
    // http://developers.whatwg.org/grouping-content.html
    $def->addElement('figure', 'Block', 'Optional: (figcaption, Flow) | (Flow, figcaption) | Flow', 'Common');
    $def->addElement('figcaption', 'Inline', 'Flow', 'Common');
    // https://html.spec.whatwg.org/dev/media.html#the-video-element
    $def->addElement('video', 'Block', 'Flow', 'Common', array(
        'controls' => 'Bool',
        'height'   => 'Length',
        'poster'   => 'URI',
        'preload'  => 'Enum#auto,metadata,none',
        'src'      => 'URI',
        'width'    => 'Length',
    ));
    $def->getAnonymousModule()->addElementToContentSet('video', 'Inline');
    // https://html.spec.whatwg.org/dev/media.html#the-audio-element
    $def->addElement('audio', 'Block', 'Flow', 'Common', array(
        'controls' => 'Bool',
        'preload'  => 'Enum#auto,metadata,none',
        'src'      => 'URI',
    ));
    $def->getAnonymousModule()->addElementToContentSet('audio', 'Inline');
    // https://html.spec.whatwg.org/dev/embedded-content.html#the-source-element
    $def->addElement('source', false, 'Empty', 'Common', array(
        'media'  => 'Text',
        'sizes'  => 'Text',
        'src'    => 'URI',
        'srcset' => 'Text',
        'type'   => 'Text',
    ));
    // https://html.spec.whatwg.org/dev/media.html#the-track-element
    $def->addElement('track', false, 'Empty', 'Common', array(
        'kind'    => 'Enum#captions,chapters,descriptions,metadata,subtitles',
        'src'     => 'URI',
        'srclang' => 'Text',
        'label'   => 'Text',
        'default' => 'Bool',
    ));
    // http://developers.whatwg.org/text-level-semantics.html
    $def->addElement('s',    'Inline', 'Inline', 'Common');
    $def->addElement('var',  'Inline', 'Inline', 'Common');
    $def->addElement('sub',  'Inline', 'Inline', 'Common');
    $def->addElement('sup',  'Inline', 'Inline', 'Common');
    $def->addElement('mark', 'Inline', 'Inline', 'Common');
    $def->addElement('wbr',  'Inline', 'Empty', 'Core');
    // http://developers.whatwg.org/edits.html
    $def->addElement('ins', 'Block', 'Flow', 'Common', array('cite' => 'URI', 'datetime' => 'CDATA'));
    $def->addElement('del', 'Block', 'Flow', 'Common', array('cite' => 'URI', 'datetime' => 'CDATA'));
    // TinyMCE
    $def->addAttribute('img', 'data-mce-src', 'Text');
    $def->addAttribute('img', 'data-mce-json', 'Text');
    // Others
    $def->addAttribute('iframe', 'allowfullscreen', 'Bool');
    $def->addAttribute('table', 'height', 'Text');
    $def->addAttribute('td', 'border', 'Text');
    $def->addAttribute('th', 'border', 'Text');
    $def->addAttribute('tr', 'width', 'Text');
    $def->addAttribute('tr', 'height', 'Text');
    $def->addAttribute('tr', 'border', 'Text');
    $def->addAttribute('img', 'data-attachment-id', 'Text');
  }

  return new HTMLPurifier($config);
}
