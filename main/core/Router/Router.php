<?php

namespace App\Core\Router;

use InvalidArgumentException;

use App\Core\Router\Route;

use App\Core\Router\RouteGroup;

use App\Core\Router\RouteCollection;

class Router
{

    /**
     * All routes
     * 
     * @var array
     */
    private static $routes = array();

    /**
     * Add a new route on any request method
     * 
     * @param string $path Route path
     * @param string $controller Controller route
     * 
     * @return Route
     */
    public function any($path, $controller)
    {
        return $this->on(null, $path, $controller);
    }

    /**
     * Add a new route on 'GET' request method
     * 
     * @param string $path Route path
     * @param string $controller Controller route
     * 
     * @return Route
     */
    public function get($path, $controller)
    {
        return $this->on('get', $path, $controller);
    }
    
    /**
     * Add a new route on 'DELETE' request method
     * 
     * @param string $path Route path
     * @param string $controller Controller route
     * 
     * @return Route
     */
    public function delete($path, $controller)
    {
        return $this->on('delete', $path, $controller);
    }

    /**
     * Add a new route group
     * 
     * @param string $parent Parent path
     * @param string $namespaces
     * @param callable $fn Callback function
     * 
     * @return RouteGroup
     */
    public function group($parent, $namespace = null, $fn = null)
    {
        $group = (new RouteGroup($parent, $this))
                    ->setNamespace($namespace);

        if(!$fn) {
            return $group;
        }

        $fn($group);
        return $group;
    }
    
    /**
     * Add a new route on 'POST' request method
     * 
     * @param string $path Route path
     * @param string $controller Controller route
     * 
     * @return Route
     */
    public function post($path, $controller)
    {
        return $this->on('post', $path, $controller);
    }

    /**
     * Add a new route on 'PATCH' request method
     * 
     * @param string $path Route path
     * @param string $controller Controller route
     * 
     * @return Route
     */
    public function patch($path, $controller)
    {
        return $this->on('patch', $path, $controller);
    }

    /**
     * Add a new route on 'POST' request method
     * 
     * @param string[] $methods Request methods
     * @param string $path Route path
     * @param string $controller Controller route
     * 
     * @return Route
     */
    public function map($methods, $path, $controller)
    {
        if(!is_array($methods)) {
            throw new InvalidArgumentException('Router::map() $method should be an array');
        }

        foreach($methods as $method)
        {
            $this->on($method, $path, $controller);
        }
    }

    /**
     * Add new route to router
     * 
     * @param string $method Request method name
     * @param string $path Route path
     * @param string $controller Controller route
     * 
     * @return Route
     */
    public function on($method, $path, $controller)
    {
        $route = new Route($method, $path, $controller);
        RouteCollection::attachRoute($route);
        return $route;
    }
}