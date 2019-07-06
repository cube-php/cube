<?php

use App\Core\Http\Controller;
use App\Core\Http\Response;

/**
 * Http functions here
 * 
 * =============================================================
 * Methods related to http requests and response should go here
 * =============================================================
 */

/**
 * Redirect path
 *
 * @param string $path
 * @param boolean $is_external
 * @return Response
 */
function redirect($path, $params = [], $is_external = false) {
    return response()->redirect($path, $params, $is_external);
}

 /**
  * Create new response instance
  *
  * @return Response
  */
function response() {
    return (new Response());
}

/**
 * Render view
 *
 * @param string $tpl
 * @return Response
 */
function view($tpl, $context = [], $run_render = true) {
    return response()->view($tpl, $context, $run_render);
}