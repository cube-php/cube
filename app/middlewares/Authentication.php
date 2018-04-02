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
        if(!Auth::user()) {
            return redirect('/login');
        }

        return $request;
    }
}