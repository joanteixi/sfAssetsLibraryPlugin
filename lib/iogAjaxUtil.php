<?php
class iogAjaxUtil {
  public static function decorateJsonResponse(sfWebResponse $response)
  {
    $response->setContentType('application/json');
    // prevent response caching on client side
    $response->addCacheControlHttpHeader('no-cache, must-revalidate');
//    $response->setHttpHeader('Expires', 'Mon, 26 Jul 1997 05:00:00 GMT');


  }
}
