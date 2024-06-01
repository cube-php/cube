<?php

namespace App\Controllers;

use Cube\Http\Response;
use Cube\Http\Request;
use Cube\Http\Controller;
use Cube\Router\Attributes\Route;

class CubeController extends Controller
{
    /**
     * Home controller
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    #[Route(method: 'GET', path: '/')]
    public function home(Request $request, Response $response)
    {
        return $response->view('home');
    }
}
