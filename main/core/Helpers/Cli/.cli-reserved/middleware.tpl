<?php

namespace App\Middlewares{subNamespace};

use App\Core\Http\Request;
use App\Core\Interfaces\MiddlewareInterface;

class {className} implements MiddlewareInterface
{
    /**
    * Trigger middleware event
    *
    * @param Request $request
    * @param array|null $args
    * @return mixed
    */
    public function trigger(Request $request, ?array $args = null)
    {   
        return $request;
    }
}