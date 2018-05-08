<?php

namespace App\Core\Router;

use App\Core\App;

use App\Core\Router\Route;

use App\Core\Http\Request;

use App\Core\Http\Response;

use App\Core\Misc\EventManager;

class RouteCollection
{

    /**
     * Routes collection
     * 
     * @var \Route[]
     */
    private static $_routes = array();

    /**
     * Routes on request method
     * 
     * @var Route[]
     */
    private static $_attached_routes = array();

    /**
     * Request interface
     * 
     * @var Request
     */
    private $_request;

    /**
     * Class constructor
     * 
     */
    public function __construct()
    {
        $this->_request = new Request;
    }

    /**
     * Attach new route to collection
     * 
     * @param Route $route Route to attach
     */
    public static function attachRoute(Route $route)
    {
        #Attach route to all routes
        static::$_routes[] = $route;

        $request = new Request;

        #attach on request method
        if($route->getMethod() && $request->getMethod() !== $route->getMethod()) {
            return $route;
        }
        static::$_attached_routes[] = $route;
        return $route;
    }

    /**
     * Build all routes
     * 
     * @return void
     */
    public function build()
    {

        $path_match_found = false;
        $current_request_method = $this->_request->getMethod();
        $raw_current_url = (string) $this->_request->url()->getPath();
        $current_url = $this->trimPath($raw_current_url);

        foreach(static::$_attached_routes as $route)
        {
            
            if($path_match_found) return true;

            #Get route regex path
            $regex_path = $route->path()->regexp();

            #Test current url
            $test = preg_match("#^{$regex_path}$#", $current_url, $matches);

            #Match found!!!
            if($test) {

                $path_match_found = true;
                $path_attributes = array_slice($matches, 1);
                $route_attributes = $route->getAttributes();

                array_walk($route_attributes, function($attribute, $index) use ($path_attributes, $route) {

                    $name = $attribute;
                    $value = $path_attributes[$index] ?? null;

                    if($route->hasOptionalParameter()) {
                        $value = substr($value, 0, strlen($value) - 1);
                    }

                    $this->_request->setAttribute($name, $value);
                });

                #Do any other events when route is matched
                EventManager::dispatchEvent(
                    $this->_request,
                    App::EVENT_ROUTE_MATCH_FOUND
                );
                
                #Get parsed response
                $response = $route->parseResponse((new Response));

                #Instantiate route controller
                $route->initController($this->_request, $response);
                
                return true;
            }
        }

        #Oh no, no matches
        EventManager::dispatchEvent(
            $this->_request,
            App::EVENT_ROUTE_NO_MATCH_FOUND
        );
    }

    /**
     * Trim route path
     *
     * @param string $path
     * @return string
     */
    public function trimPath($path)
    {
        $path = preg_replace('#([\/]{1,})#', '/', $path);
        return $path;
    }
}