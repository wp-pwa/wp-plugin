<?php

class Frontity_Miscellanea
{
  // Adds Cross origin * to the header
  function allow_origin()
  {
    if (!headers_sent()) {
      header("Access-Control-Allow-Origin: *");
      header('Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE');
      header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    }
  }

  // Adds resizement to WP embedded posts.
  function send_post_embed_height()
  {
    echo "<script>"
      . "window.parent.postMessage({"
      . "sentinel:'amp',type:'embed-size',height:document.body.scrollHeight"
      . "},'*');"
      . "</script>";
  }
}