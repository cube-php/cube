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
 * -----------------------------------------------------
 * Globally assigned variables
 * -----------------------------------------------------
 */
require_once '..' . DS . 'main' . DS . 'boot' . DS . 'vars.php';

/**
 * -----------------------------------------------------
 * Use app bootstraper
 * -----------------------------------------------------
 */
require_once MAIN_APP_PATH . DS . 'core' . DS . 'bootstrap.php';

/**
 * -----------------------------------------------------
 * Let's start the app.
 * ----------------------------------------------------
 * Create an instance of app
 */
$app = new App\Core\App;

/**
 * ------------------------------------------------------
 * On your code, get ready, START!!!
 * ------------------------------------------------------
 * Fire the app!
 */
$app->run();