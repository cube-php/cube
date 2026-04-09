<?php

namespace App\Controllers;

use Cube\Http\Response;
use Cube\Http\Request;
use Cube\Http\Controller;
use Cube\Router\Attributes\Get;

class CubeController extends Controller
{
    /**
     * Home controller
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    #[Get(path: '/')]
    public function home(Request $request, Response $response)
    {
        return $response->view('home');
    }
}
