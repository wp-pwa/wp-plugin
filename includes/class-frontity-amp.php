<?php

class Frontity_Amp
{
  protected $should_inject;
  protected $link_string;

  // Check if should inject the AMP canonical link
  // to the header html.
  function check_if_should_inject()
  {
    $amp_active = Frontity_Request::get('amp_active');
    $amp_forced = Frontity_Request::get('amp_forced');
    $excluded = Frontity_Request::get('excluded');

    $this->should_inject = $amp_forced
      || ($amp_active && is_single() && !$excluded);
  }
  
  // Generates the string with the AMP canonical link.
  function generate_link_string()
  {
    $type = Frontity_Request::get('type');
    $id = Frontity_Request::get('id');
    $site_id = Frontity_Request::get('site_id');
    $amp_server = Frontity_Request::get('amp_server');
    $initial_url = Frontity_Request::get('initial_url');
    $permalink = get_permalink($id);

    $path = parse_url($permalink, PHP_URL_PATH);
    $query = "?siteId={$site_id}&type={$type}&id={$id}&initialUrl={$initial_url}";
    $amp_url = $amp_server . $path . $query;

    $this->link_string = "<link rel='amphtml' href='{$amp_url}'>\n";
  }

  function inject_header_html()
  {
    if ($this->should_inject)
      echo $this->link_string;
  }
}