<?php

namespace App\Core;

use App\Core\Http\Request;

use App\Core\Http\Response;

use App\Core\Router\RouteCollection;

use App\Core\Misc\EventManager;

use App\Core\Http\Session;

class App
{

    /**
     * App is development
     * 
     * @var int
     */
    const APP_MODE_DEVELOPMENT = 101;

    /**
     * App is production
     * 
     * @var int
     */
    const APP_MODE_PRODUCTION = 102;

    /**
     * App event
     * 
     * Before app runs
     * 
     * @var string
     */
    const EVENT_BEFORE_RUN = 'onBeforeAppStart';

    /**
     * App event
     * 
     * Before app runs
     * 
     * @var string
     */
    const EVENT_RUNNING = 'onAppRunning';

    /**
     * On route match found event
     * 
     * Events when route match is found
     * 
     * @var string
     */
    const EVENT_ROUTE_MATCH_FOUND = 'onRouteMatchFound';

    /**
     * Event when not route match is found
     * 
     * @var string
     */
    const EVENT_ROUTE_NO_MATCH_FOUND = 'onRouteNoMatchFound';

    /**
     * Event when app is in development mode
     * 
     * @var string
     */
    const EVENT_APP_ON_DEVELOPMENT = 'onAppDevelopment';
    
    /**
     * Event when app is in production mode
     * 
     * @var string
     */
    const EVENT_APP_ON_PRODUCTION = 'onAppProduction';

    /**
     * Loaded configurations
     * 
     * @var array
     */
    private static $config = array();

    /**
     * Request
     * 
     * @var \App\Core\Http\Request
     */
    private $_request;

    /**
     * Class constructor
     * 
     * @return void
     */
    public function __construct()
    {
        
        $this->_request = new Request;

        EventManager::dispatchEvent(
            $this,
            self::EVENT_BEFORE_RUN
        );
    }

    /**
     * Class destruct
     * 
     * @return void
     */
    public function __destruct()
    {
        EventManager::dispatchEvent(
            $this,
            self::EVENT_RUNNING
        );
    }

    /**
     * Get configuration
     * 
     * @param string $name Configuration name
     * 
     * @return array
     */
    public static function getConfigByName($name)
    {
        return static::$config[$name] ?? null;
    }

    /**
     * Load configuration by file
     * This will load configuration even when app is has not been started
     *
     * @param string $name Config name
     * @return array
     */
    public static function getConfigByFile($name)
    {
        if(isset(static::$config[$name])) {
            return static:: $config[$name];
        }

        $path = CONFIG_PATH . DS . $name . '.php';
        $config = require_once $path;
        
        static::$config[$name] = $config;
        return $config;
    }

    /**
     * Returns specified enviroment
     * 
     * @return string
     */
    public static function getEnviroment()
    {
        $config = static::getConfigByName('app');
        return $config['app_mode'];
    }

    /**
     * Returns true if is in development mode
     * 
     * @return boolean
     */
    public static function isDevelopment()
    {
        return !static::isProduction();
    }
    
    /**
     * Returns true if is in production mode
     * 
     * @return boolean
     */
    public static function isProduction()
    {
        $config = static::getConfigByName('app');
        return ($config['app_mode'] === self::APP_MODE_PRODUCTION);
    }

    /**
     * Start the app
     * 
     * @return void
     */
    public function run()
    {
        #Load configurations
        $this->loadConfig();

        #Configure application
        $this->configure();
        
        #System helpers
        $this->initSystemHelpers();

        #Then, initialize sessions first
        $this->initSessions();

        #Then custom helpers
        $this->initHelpers();

        #Then routes
        $this->initRoutes();
    }

    /**
     * App mode checker
     * 
     * @param int $mode Registered mode
     * 
     * @return void
     */
    private function appModeChecker($mode = null)
    {

        #If no app mode, let's set it default to development
        $mode = $mode ?? self::APP_MODE_DEVELOPMENT;

        switch($mode)
        {
            #On development mode
            case self::APP_MODE_DEVELOPMENT:
                #Yea we want this on
                ini_set('display_errors', 'On');

                #Set error reporting
                error_reporting(E_ALL);

                #Let's initiate other registered events
                EventManager::dispatchEvent(
                    $this,
                    self::EVENT_APP_ON_DEVELOPMENT
                );
            break;

            #On production mode
            case self::APP_MODE_PRODUCTION:
                #Yea we want this off
                ini_set('display_errors', 'Off');
                
                #No error reporting
                error_reporting(0);

                #Let's initiate other registered events
                EventManager::dispatchEvent(
                    $this,
                    self::APP_MODE_PRODUCTION
                );
            break;
        }
    }
    
    /**
     * Configure Application
     * 
     * @return
     */
    private function configure()
    {
        #Load configuration
        $config = static::getConfigByName('app');

        #If configuration, kill the app
        if(!$config) {
            static::kill('Unable to load app configuration file');
        }

        #Check app mode
        $this->appModeChecker($config['app_mode'] ?? null);

        #Check for https
        $this->forceHTTPs($config['force_https'] ?? null);
    }

    /**
     * Force HTTPs request
     * 
     * @param bool $redirect
     * 
     * @return void;
     */
    private function forceHTTPs($redirect)
    {
        $scheme = strtolower($this->_request->url()->getScheme());
        $secure_scheme = 'https';

        if($scheme == $secure_scheme) return true;

        #If user has set force_https to false
        if(!$redirect) return;

        $url  = $this->_request->url()->getUrlWithoutScheme();
        $rdr_url = $secure_scheme . $url;
        
        $response = (new Response())
            ->withStatusCode(301)
            ->withHeader('location', $rdr_url)
            ->write(null);

        exit;
    }

    /**
     * Init  Helpers
     * 
     * @return void
     */
    private function initHelpers()
    {
        $this->loadDirFiles(MAIN_APP_PATH . DS . 'helpers');
    }

    /**
     * Make the app listen to routes
     * 
     * @return void
     */
    private function initRoutes()
    {
        $routes = new RouteCollection();
        $routes->build();
    }   

    /**
     * Initialize sessions
     * 
     * @return void
     */
    private function initSessions()
    {
        return Session::createInstance();
    }

    /**
     * Initialize system assigned helpers
     * 
     * @return void
     */
    private function initSystemHelpers()
    {
        $this->loadDirFiles(__DIR__ . DS . 'functions');
    }

    /**
     * Kill the app process
     *
     * @param string $reason Reason to kill the app
     *  
     * @return void
     */
    private function kill($reason)
    {
        die((string) $reason);
    }
    

    /**
     * Loading configuration files
     * 
     * @return void
     */
    private function loadConfig()
    {
        $this->loadDirFiles(CONFIG_PATH);
    }

    /**
     * Loading configuration files
     * 
     * @return void
     */
    private function loadDirFiles($dir_path)
    {

        #Oh dots
        $dots = array('.', '..');

        $raw_filelist = scandir($dir_path);
        $files = array_diff($raw_filelist, $dots);
        
        #No configuration files
        if(!count($files)) return null;

        foreach($files as $file)
        {
            $file_path = $dir_path . DS . $file;
            $name_vars = explode('.', $file);
            $extension = strtolower($ext = array_slice($name_vars, -1)[0]);
            $name = implode('.', $left_vars = array_splice($name_vars, 0, -1));

            if($extension === 'php' && !isset(static::$config[$name])) {
                static::$config[$name] = require_once($file_path);
            }
        }
    }
}