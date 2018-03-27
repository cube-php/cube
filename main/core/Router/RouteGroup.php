<?php

namespace App\Core\Router;

use InvalidArgumentException;

use App\Core\Router\Router;

class RouteGroup
{

    /**
     * Multi route method name
     * 
     * @var string
     */
    private static $_multi_method_name = 'map';

    /**
     * Route group path
     * 
     * @var string
     */
    private $_parent;

    /**
     * Routes 
     * 
     * @var \App\Core\Router\Router
     */
    private $_router;

    /**
     * Route Namespace
     *
     * @var string
     */
    private $_namespace;

    /**
     * Route group constructor
     * 
     * @param string $path
     */
    public function __construct($path, Router $router)
    {
        $this->_parent = $path;
        $this->_router = $router;
    }

    /**
     * Register namespace for route group
     *
     * @param string $namespace
     * @return self
     */
    public function setNamespace($namespace)
    {
        $this->_namespace = $namespace;
        return $this;
    }

    /**
     * Router Calls
     * 
     * @param string $name Method name
     * @param array $args Arguements
     * 
     * @return void
     * 
     * @throws \InvalidArgumentException
     */
    public function __call($name, $args)
    {

        $num_args = count($args);

        if($name === static::$_multi_method_name && $num_args != 3) {
            throw new InvalidArgumentException
                ('"map" method should contain 3 arguments (string[] $methods, string $path, string $controller)');
        }

        if($num_args != 2 && $name !== static::$_multi_method_name) {
            throw new InvalidArgumentException
                ('"' . $name . '" method should contain 2 arguments (string $path, string $controller)');
        }

        #Arrange path
        $old_path = ($num_args == 2) ? $args[0] : $args[1];
        $new_path = $this->_parent . $old_path;
        
        #name path
        if($name === static::$_multi_method_name) {
            return $this->_router->map($args[0], $new_path, $args[2]);
        }

        #register path
        $registered_route = $this->_router->{$name}($new_path, $args[1]);

        if($this->_namespace) {
            $registered_route->setNamespace($this->_namespace);
        }

        #Return registered route
        return $registered_route;
    }
}