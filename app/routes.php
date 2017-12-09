<?php

/**
 * Use the router module
 */
use App\Core\Router\Router;

/**
 * Create instance of router
 */
$router = new Router();

/**
 * Add a route
 */
$router->any('/', 'CubeController.home');