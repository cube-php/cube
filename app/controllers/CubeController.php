<?php

namespace App\Controllers;

use App\Core\Http\Response;
use App\Core\Http\Request;
use App\Core\Http\Controller;
use App\Core\Misc\InputValidator;

class CubeController extends Controller
{
    /**
     * Home controller
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function home(Request $request, Response $response)
    {
        return $response->view('home');
    }
}