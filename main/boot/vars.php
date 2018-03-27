<?php

/**
 * ------------------------------------------------
 * APP DIRECTORIES
 * ------------------------------------------------
 * All required app directories specified
 * 
 */


#Boot directory
define('BOOT_PATH', __DIR__);

#Root directory
define('APP_PATH', BOOT_PATH . DS . '..' . DS . '..');

#App directory
define('CORE_APP_PATH', APP_PATH . DS . 'main');

#Main app directory
define('MAIN_APP_PATH', APP_PATH . DS . 'app');

# Set app view directory
define('VIEW_PATH', MAIN_APP_PATH . DS . '/views');

# Configuration directory
define('CONFIG_PATH', APP_PATH . DS . 'config' . DS);

# Storage path
define('APP_STORAGE', APP_PATH . DS . 'storage' .  DS);

#Controllers Path
define('APP_CONTROLLERS_PATH', MAIN_APP_PATH . DS . 'controllers');

#Models Path
define('APP_MODELS_PATH', MAIN_APP_PATH . DS . 'models');

#Providers Path
define('APP_PROVIDERS_PATH', MAIN_APP_PATH . DS . 'providers');

#Providers Path
define('APP_EXCEPTIONS_PATH', MAIN_APP_PATH . DS . 'exceptions');

#Providers Path
define('APP_HELPERS_PATH', MAIN_APP_PATH . DS . 'helpers');

#Providers Path
define('APP_PUBLIC_STORAGE_PATH', APP_STORAGE . DS . 'public');