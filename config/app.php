<?php

/**
 * -----------------------------------------------
 * App configuration follows
 * -----------------------------------------------
 * This file and the contents within are required
 * Deleting this file may resulting in fatal errors
 * Except, Of course you know what you're doing
 */

use App\Core\App;

return array(

    /**
     * The current mode of the app you're developing
     * 
     * Default: App::APP_MODE_DEVELOPMENT
     * Errors will be thrown whilst this value is set
     * 
     * Switch to App::APP_MODE_PRODUCTION
     * When live, errors will be hidden
     */
    'app_mode' => App::APP_MODE_DEVELOPMENT,

    /**
     * Default timezone for your app
     * 
     * 
     */
    'time_zone' => 'Africa/Lagos',

    /**
     * Force HTTPs connection
     * 
     * @var Boolean
     */
    'force_https' => false,

    /**
     * Specify app directory
     * 
     * if app is not installed in the root directory
     */
    'directory' => ''

);