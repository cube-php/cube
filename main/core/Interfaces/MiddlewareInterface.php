<?php

namespace App\Core\Interfaces;

use App\Core\Http\Request;

use App\Core\Http\Response;

interface MiddlewareInterface
{
    public function trigger(Request $request, ?array $args = null);
}