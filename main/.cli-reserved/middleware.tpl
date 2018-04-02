<?php

namespace App\Middlewares;

use App\Core\Http\Request;
use App\Core\Interfaces\MiddlewareInterface;

class {className} implements MiddlewareInterface
{
    /**
    * Trigger middleware event
    *
    * @param Request $request
    * @return mixed
    */
    public function trigger(Request $request)
    {   
        return $request;
    }
}