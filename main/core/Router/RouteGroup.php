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
    private static $multi_method_name = 'map';

    /**
     * Route group path
     * 
     * @var string
     */
    private $parent;

    /**
     * Routes 
     * 
     * @var \App\Core\Router\Router
     */
    private $router;

    /**
     * Route group constructor
     * 
     * @param string $path
     */
    public function __construct($path, Router $router)
    {
        $this->parent = $path;

        $this->router = $router;
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

        if($name === static::$multi_method_name && $num_args != 3) {
            throw new InvalidArgumentException
                ('"map" method should contain 3 arguments (string[] $methods, string $path, string $controller)');
        }

        if($num_args != 2 && $name !== static::$multi_method_name) {
            throw new InvalidArgumentException
                ('"' . $name . '" method should contain 2 arguments (string $path, string $controller)');
        }

        #Arrange path
        $old_path = ($num_args == 2) ? $args[0] : $args[1];
        $new_path = $this->parent . $old_path;
        
        #name path
        if($name === static::$multi_method_name) {
            return $this->router->map($args[0], $new_path, $args[2]);
        }

        #register path
        return $this->router->{$name}($new_path, $args[1]);
    }
}