<?php

class Frontity_Miscellanea
{
  // Adds Cross origin * to the header
  function allow_origin()
  {
    if (!headers_sent()) header("Access-Control-Allow-Origin: *");
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