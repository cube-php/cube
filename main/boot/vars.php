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