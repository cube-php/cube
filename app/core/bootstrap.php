<?php

/**
 * ============================================================
 *      BOOTSTRAP
 * ============================================================
 * 
 * This is a core system file
 * Every logic registered here is executed every time the app being loaded
 * 
 * To ensure faster load times of the app, every logic here should be simple
 * And should not contain very complex and time taking commands
 */

use App\Core\App;
use App\Core\Http\Request;
use App\Core\Misc\EventManager;

EventManager::on(App::EVENT_ROUTE_NO_MATCH_FOUND, function(Request $request) {
    return response()->view('default.404', [
        '_path' => $request->url()->getPath(),
        '_request_method' => $request->getMethod()
    ]);
});