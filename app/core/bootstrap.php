<?php

use App\Core\App;
use App\Core\Misc\EventManager;

EventManager::on(App::EVENT_ROUTE_NO_MATCH_FOUND, function($path) {
    return response()->view('404');
});