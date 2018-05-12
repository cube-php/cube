<?php

namespace App\Middlewares;

use App\Core\Http\Request;
use App\Core\Interfaces\MiddlewareInterface;

use App\Core\Tools\Auth;

class Authentication implements MiddlewareInterface
{
    /**
    * Trigger middleware event
    *
    * @param Request $request
    * @return mixed
    */
    public function trigger(Request $request)
    {   
        $user = Auth::user();

        if(!$user) {
            return redirect('/login');
        }

        $request->setCustomMethod(function () use ($user) {
            return $user;
        });

        return $request;
    }
}