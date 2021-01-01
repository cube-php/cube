<?php

define('DS', DIRECTORY_SEPARATOR);

/**
 * ----------------------------------------------------
 * Oh why not, Use composer autoloader
 * ----------------------------------------------------
 * Use composer autoload to load dependecies
 * and autoload components
 */
require_once '..' . DS . 'vendor' . DS . 'autoload.php';

/**
 * ------------------------------------------------------
 * Register all app directories
 * ------------------------------------------------------
 */
Cube\App::registerPath();

/**
 * -----------------------------------------------------
 * Use app bootstraper
 * -----------------------------------------------------
 */
require_once APP_PATH . DS . 'core' . DS . 'bootstrap.php';

/**
 * -----------------------------------------------------
 * Let's start the app.
 * ----------------------------------------------------
 * Create an instance of app
 */
$app = new Cube\App;

/**
 * ------------------------------------------------------
 * On your code, get ready, START!!!
 * ------------------------------------------------------
 * Fire the app!
 */
$app->run();