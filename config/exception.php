<?php

use Cube\App\AppExceptionsHandler;
use Cube\Exceptions\RouteNotFoundException;
use Cube\Http\Request;
use Cube\Http\Response;

return function (AppExceptionsHandler $handler) {
    $handler->on(RouteNotFoundException::class, function (Request $request): Response {
        return response()->view('404', [
            'request' => $request
        ]);
    });
};
