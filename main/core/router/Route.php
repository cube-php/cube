<?php

namespace App\Core\Router;

use InvalidArgumentException;

use App\Core\Router\RouteCollection;

use App\Core\Router\RouteParser;

use App\Core\Http\Request;

use App\Core\Http\Response;

class Route
{

    /**
     * Route method
     * 
     * @var string[]
     */
    private $_method = array();

    /**
     * Route path
     * 
     * @var string
     */
    private $_path;

    /**
     * Route controller
     * 
     * @var string[]
     */
    private $_controller = array();

    /**
     * Controllers namespace
     * 
     * @var string
     */
    private static $controllerNamespace = 'App\\Controllers\\';

    /**
     * Route attributes
     * 
     * @var string[]
     */
    private $attributes = array();

    /**
     * Class constructor
     * 
     * @param string|string[] $method
     * @param string $path
     * @param string $controller
     */
    public function __construct($method, $path, $controller)
    {
        $this->setMethod(strtolower($method));
        $this->setPath($path);
        $this->setController($controller);
    }

    /**
     * Set route controller
     * 
     * @param string $controller Controller pattern
     * 
     * <<Controller pattern>>
     * If your controller class is HomeController
     * And you want route to focus on HomeController::index() method
     * Set controller path to
     * 
     * "HomeController.index"
     * 
     * <</Controller pattern>>
     * 
     * @return void
     * 
     * @throws \InvalidArgumentException if $controller is not callable
     */
    public function setController($controller)
    {
        $controller_vars = explode('.', $controller);
        $controller_vars_count = count($controller_vars);
        
        if($controller_vars_count < 2 || $controller_vars_count > 2) {
            throw new InvalidArgumentException
                ('Controller should be passed as "ClassName.methodName"');
        }

        $controller_class_name = static::$controllerNamespace . $controller_vars[0];
        $controller_method_name = $controller_vars[1];

        if(!is_callable($controller_class_name . '::' . $controller_method_name)) {
            throw new InvalidArgumentException('"' . $controller . '" on route is not callable');
        }

        $this->_controller = array(
            'class_name' => $controller_class_name,
            'method_name' => $controller_method_name
        );
    }

    /**
     * Set route path
     * 
     * @param string $path Route path
     * 
     * @return void
     */
    public function setPath($path)
    {
        $this->_path = $path;
    }

    /**
     * Set route methods
     * 
     * @param string|string[] $method String or array of HTTP request methods
     * 
     * @return void
     */
    public function setMethod($method)
    {
        $this->_method = $method;
    }

    /**
     * Set attributes found in route path
     * 
     * @param string $name Attribute field name
     * 
     * @return void
     */
    public function setAttribute($name)
    {
        $this->attributes[] = $name;
    }

    /**
     * Return all attributes in route
     * 
     * @return array[]
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Returns the route's request method
     * 
     * @return string Request method
     */
    public function getMethod()
    {
        return $this->_method;
    }

    /**
     * Returns the routes controller object
     * 
     * @return object
     */
    public function getController()
    {
        return $this->_controller;
    }

    /**
     * Call the controller
     * 
     * @return void
     */
    public function initController(Request $request, Response $response)
    {
        $class = $this->_controller['class_name'];
        $method = $this->_controller['method_name'];

        $controller = new $class;
        return $controller->{$method}($request, $response);
    }

    /**
     * Returns the specified route path
     * 
     * @return string
     */
    public function getPath()
    {
        return $this->_path;
    }

    /**
     * Return the regexp-fied route path
     * 
     * @return \App\Core\Tools\RouteParser
     */
    public function path()
    {
        return new RouteParser($this);
    }

}