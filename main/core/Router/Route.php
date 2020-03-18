<?php

namespace App\Core\Router;

use Closure;

use InvalidArgumentException;

use App\Core\Router\RouteCollection;
use App\Core\Router\RouteParser;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\AnonController;

class Route
{

    /**
     * Prefix to register a view rather than a controller for route
     *
     * @var string
     */
    const VIEW_PREFIX = '@';

    /**
     * Prefix to register controller from base controller namespace
     * 
     * @var string
     */
    const CONTINUOUS_NAMESPACE_PREFIX = '\\';

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
     * @var string[]|Closure
     */
    private $_controller = array();

    /**
     * Controllers namespace
     * 
     * @var string
     */
    private $_controllerNamespace = 'App\\Controllers';

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
     * CORS status
     *
     * @var boolean
     */
    private $_enable_cors = true;

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
     * Add specified middlewares to request
     *
     * @param Request $request
     * @return Request|null
     */
    public function engageMiddleware(Request $request)
    {
        return $request->useMiddleware($this->_middlewares);
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
     * @return self
     * 
     * @throws \InvalidArgumentException if $controller is not callable
     */
    public function setController($controller)
    {
        $this->_controller = $controller;
        return $this;
    }

    /**
     * Set route path
     * 
     * @param string $path Route path
     * 
     * @return self
     */
    public function setPath($path)
    {
        $this->_path = $path;
        return $this;
    }

    /**
     * Set route methods
     * 
     * @param string|string[] $method String or array of HTTP request methods
     * 
     * @return self
     */
    public function setMethod($method)
    {
        $this->_method = $method;
        return $this;
    }

    /**
     * Set route namespace
     *
     * @param string $namespace
     * @return self
     */
    public function setNamespace($namespace)
    {
        $is_continuous = substr($namespace, 0, 1) === self::CONTINUOUS_NAMESPACE_PREFIX;
        $this->_controllerNamespace = $is_continuous ?
            $this->_controllerNamespace . $namespace : $namespace;

        return $this;
    }

    /**
     * Set whether or not to allow CORS
     *
     * @param boolean $cors
     * @return self
     */
    public function setEnableCors(bool $cors)
    {
        $this->_enable_cors = $cors;
        return $this;
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
        return $this;
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
        return $this;
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
        #Check if route is registered with a view
        $view_file_path = $this->_isReturnView();
        if($view_file_path) {
            return $response->view($view_file_path);
        }

        #Parse controller
        $parse = $this->_parseController();

        if($this->_is_callble_controller) {
            $controller = Closure::bind($this->_controller, new AnonController(), AnonController::class);
            return $this->_analyzeControllerResult($controller, $request, $response);
        }

        $class = $this->_controller['class_name'];
        $method = $this->_controller['method_name'];

        $controller = new $class;

        if(!is_callable([$class, $method])) {
            throw new InvalidArgumentException
                ("{$class}::{$method}() on route \"{$this->getPath()}\" is not a valid callable method");
        }

        return $this->_analyzeControllerResult([$controller, $method], $request, $response);
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
     * Custom response values/header
     *
     * @param Response $response
     * @return Response
     */
    public function parseResponse(Response $response)
    {
        $cors_method = $this->_enable_cors ? 'enableCors' : 'disableCors';
        $response = call_user_func([$response, $cors_method]);
        return $response;
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

    /**
     * Analyze route's controller
     *
     * @param string|array $controller
     * @param Request $request
     * @param Response $response
     * @return string|Response result
     */
    private function _analyzeControllerResult($controller, Request $request, Response $response)
    {
        $result = call_user_func_array($controller, [$request, $response]);

        if(is_string($result)) {
            return $response->write($result);
        }

        return $result;
    }

    /**
     * Return value if route is registered to return only view
     *
     * @return boolean|string
     */
    private function _isReturnView()
    {
        $controller = $this->_controller;

        if(!is_string($controller)) {
            return false;
        }

        $first_value = substr($controller, 0, 1);

        if($first_value != self::VIEW_PREFIX) {
            return false;
        }

        return substr($controller, 1);
    }

    /**
     * Parse controller
     *
     * @return bool
     */
    private function _parseController()
    {

        $controller = $this->_controller;

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

        $controller_class_name = $this->_controllerNamespace . '\\' . $controller_vars[0];
        $controller_method_name = $controller_vars[1];

        $this->_controller = array(
            'class_name' => $controller_class_name,
            'method_name' => $controller_method_name
        );

        return true;
    }
}