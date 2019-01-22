<?php

class Frontity_Auto_Injector
{
  function __construct()
  {
    // Stores the header output in a buffer.
    add_action('get_header', array($this, 'store_header_output'));
		// Retrieves the header output from the buffer.
    add_action('wp_head', array($this, 'get_header_output'), 0);
    // Adds the injector to the header.
    add_filter('frontity_header_html', array($this, 'add_injector_to_header'));
  }

  function store_header_output()
  {
    ob_start();
  }

  function get_header_output()
  {
    // String whre the header content will be stored.
    $header_html = '';
    
    // Get the content from and clean every level of the buffer.
    while (ob_get_level()) {
      $header_html .= ob_get_clean();
    }
		
		// Apply any filters to the final output.
    echo apply_filters('frontity_header_html', $header_html);
  }

  function add_injector_to_header($html)
  {
    return str_replace('<head>', '<head><script src="hola amigos"></script>', $html);
  }
}

new Frontity_Auto_Injector();