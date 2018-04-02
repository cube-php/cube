<?php

use App\Core\App;
use App\Core\Http\Request;
use App\Core\Misc\EventManager;

EventManager::on(App::EVENT_ROUTE_NO_MATCH_FOUND, function(Request $request) {
    return response()->view('404', [
        '_path' => $request->url()->getPath(),
        '_request_method' => $request->getMethod()
    ]);
});