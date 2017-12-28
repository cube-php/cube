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
     * @var \App\Core\Tools\Route[]
     */
    private static $routes = array();

    /**
     * Routes on request method
     * 
     * @var \App\Core\Tools\Route[]
     */
    private static $attached_routes = array();

    /**
     * Request interface
     * 
     * @var \App\Core\Http\Request
     */
    private $request;

    /**
     * Class constructor
     * 
     */
    public function __construct(){
        $this->request = new Request;
    }

    /**
     * Attach new route to collection
     * 
     * @param $route \App\Core\Tools\Route
     */
    public static function attachRoute(Route $route)
    {
        #Attach route to all routes
        static::$routes[] = $route;

        $request = new Request;

        #attach on request method
        if($route->getMethod() && $request->getMethod() !== $route->getMethod()) {
            return $route;
        }
        static::$attached_routes[] = $route;
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
        $current_request_method = $this->request->getMethod();
        $raw_current_url = (string) $this->request->url()->getPath();
        $current_url = $this->trimPath($raw_current_url);

        foreach(static::$attached_routes as $route)
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

                array_walk($route_attributes, function($attribute, $index) use ($path_attributes) {

                    $name = $attribute;
                    $value = $path_attributes[$index] ?? null;

                    $this->request->setAttribute($name, $value);
                });

                #Do any other events when route is matched
                EventManager::dispatchEvent(
                    $current_url,
                    App::EVENT_ROUTE_MATCH_FOUND
                );

                #Set Middlewares
                $request = $route->engageMiddleware($this->request);

                #Instantiate route controller
                $route->initController($request, new Response);
                
                return true;
            }
        }

        #Oh no, no matches
        EventManager::dispatchEvent(
            $current_url,
            App::EVENT_ROUTE_NO_MATCH_FOUND
        );
    }

    public function trimPath($path)
    {
        $path = preg_replace('#([\/]{1,})#', '/', $path);
        return $path;
    }
}