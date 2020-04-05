<?php

namespace App\Events;

use App\Core\Http\Request;

class RouteNotFoundEvent
{
    public static function handle(Request $request)
    {
        return view('404', [
            'request' => $request
        ]);
    }
}