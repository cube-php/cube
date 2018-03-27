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
     * @var string[]|callable
     */
    private $_controller = array();

    /**
     * Controllers namespace
     * 
     * @var string
     */
    private static $_controllerNamespace = 'App\\Controllers\\';

    /**
     * Route attributes
     * 
     * @var string[]
     */
    private $_attributes = array();

    /**
     * Set if the controller is a callable
     *
     * @var boolean
     */
    private $_is_callble_controller = false;

    /**
     * Has optional parameter
     *
     * @var boolean
     */
    private $_has_optional_parameter = false;

    /**
     * Middlewares
     * 
     * @var string[]
     */
    private $_middlewares = [];

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
     * Add middlewares to request
     *
     * @return void
     */
    public function engageMiddleware(Request $request)
    {
        foreach($this->getMiddlewares() as $middleware) {
            $request->useMiddleware($middleware);
        }

        return $request;
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
        if(is_callable($controller)) {
            $this->_is_callble_controller = true;
            $this->_controller = $controller;
            return true;
        }

        $controller_vars = explode('.', $controller);
        $controller_vars_count = count($controller_vars);
        
        if($controller_vars_count < 2 || $controller_vars_count > 2) {
            throw new InvalidArgumentException
                ('Controller should be passed as "ClassName.methodName"');
        }

        $controller_class_name = static::$_controllerNamespace . $controller_vars[0];
        $controller_method_name = $controller_vars[1];

        $this->_controller = array(
            'class_name' => $controller_class_name,
            'method_name' => $controller_method_name
        );

        return true;
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
        $this->_attributes[] = $name;
    }

    /**
     * Set if route has optional parameter
     *
     * @param string $val
     * @return self
     */
    public function setHasOptionalParameter(bool $val)
    {
        $this->_has_optional_parameter = $val;
    }

    /**
     * Return all attributes in route
     * 
     * @return array[]
     */
    public function getAttributes()
    {
        return $this->_attributes;
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
     * Return Assigned Middlewares
     *
     * @return string[]
     */
    public function getMiddlewares()
    {
        return $this->_middlewares;
    }

    /**
     * Check if route has an optional parameter
     *
     * @return boolean
     */
    public function hasOptionalParameter()
    {
        return $this->_has_optional_parameter;
    }

    /**
     * Call the controller
     * 
     * @return void
     */
    public function initController(Request $request, Response $response)
    {
        if($this->_is_callble_controller) {
            return call_user_func_array($this->_controller, [$request, $response]);
        }

        $class = $this->_controller['class_name'];
        $method = $this->_controller['method_name'];

        $controller = new $class;

        if(!is_callable("{$class}::{$method}")) {
            throw new InvalidArgumentException
                ("{$class}::{$method}() on route \"{$this->getPath()}\" is not a valid callable method");
        }

        return call_user_func_array([$controller, $method], [$request, $response]);
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
     * @return RouteParser
     */
    public function path()
    {
        return new RouteParser($this);
    }

    /**
     * Set Route middlewares
     *
     * @param string|array $wares Middlewares
     * @return self
     */
    public function use($wares)
    {
        if(is_array($wares)) {
            foreach($wares as $ware) {
                $this->_middlewares[] = $ware;
            }
            return $this;
        }

        $this->_middlewares[] = $wares;
        return $this;
    }
}