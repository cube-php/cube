<?php

namespace App\Events;

use App\Core\Http\Request;

class RouteNotFoundEvent
{
    public static function handle(Request $request)
    {
        return view('default.404', [
            '_path' => $request->url()->getPath(),
            '_request_method' => $request->getMethod()
        ]);
    }
}