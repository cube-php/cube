<?php
use App\Core\App;
use App\Events\RouteNotFoundEvent;

return array(
    App::EVENT_ROUTE_NO_MATCH_FOUND => [
        RouteNotFoundEvent::class
    ]
);