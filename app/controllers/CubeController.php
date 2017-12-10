<?php

namespace App\Controllers;

use App\Core\Http\Response;
use App\Core\Http\Request;
use App\Core\Http\Controller;

use App\Core\Http\Env;

class CubeController extends Controller
{
    public function home(Request $request, Response $response) {
        return $response->renderView('home');
    }
}