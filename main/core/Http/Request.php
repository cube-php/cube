<?php

namespace App\Core\Http;

use InvalidArgumentException;
use App\Core\Interfaces\RequestInterface;

use App\Core\Http\Server;
use App\Core\Http\Headers;
use App\Core\Http\Session;
use App\Core\Http\Uri;

use App\Core\Misc\FilesParser;
use App\Core\Misc\Inputs;
use App\Core\Misc\Input;
use App\Core\App;

class Request implements RequestInterface
{
    /**
     * Request parameters
     * 
     * @var array
     */
    private $attributes = array();

    /**
     * Middlewares
     * 
     * @var string[]
     */
    public $_wares = array();

    /**
     * Server
     * 
     * @var Server
     */
    private static $_server;

    /**
     * Header
     * 
     * @var Headers
     */
    private static $_headers;

    /**
     * Url
     *
     * @var Uri
     */
    private static $_url;
    
    /**
     * Input
     *
     * @var Input
     */
    private static $_input;

    /**
     * Class constructor
     * 
     */
    public function __construct()
    {
        //Nothing to do here
    }

    /**
     * Call middlewares
     * 
     * @param string $method Method name
     * @param string[] $args Method arguments
     * 
     * @return callable
     */
    public function __call($method, $args)
    {
        $ware = array_key_exists($method, $this->_wares);

        if(!$ware) {
            throw new InvalidArgumentException
                ('Custom method "'. $method .'" not assigned');
        }
        
        return call_user_func($this->_wares[$method], $args);
    }

    /**
     * Getter
     * 
     * @param string $name Getter name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->getAttribute($name);
    }

    /**
     * Get request body
     *
     * @return object
     */
    public function getBody()
    {
        $contents = file_get_contents('php://input');
        return $contents;
    }

    /**
     * Return parsed request body
     *
     * @return string JSON parsed string
     */
    public function getParsedBody()
    {
        return json_decode($this->getBody());
    }

    /**
     * Return request headers
     *
     * @return Headers
     */
    public function getHeaders()
    {
        if(static::$_headers) {
            return static::$_headers;
        }

        static::$_headers = new Headers();
        return static::$_headers;
    }

    /**
     * Return request server variables
     *
     * @return Server;
     */
    public function getServer()
    {
        if(static::$_server) {
            return static::$_server;
        }

        static::$_server = new Server();
        return static::$_server;
    }

    /**
     * Get uploaded files
     *
     * @param string $index Uploaded file name path
     *
     * @return UploadedFile|array
     */
    public function getUploadedFiles($index = null)
    {
        $parser = new FilesParser($_FILES);
        $parsed_files = $parser->parse();

        if(!$index) return $parsed_files;

        $indexes = explode('.', $index);
        $trimmed_indexes = array_map('trim', $indexes);

        foreach($trimmed_indexes as $file_index)
        {
            if(is_null($parsed_files)) return null;
            $parsed_files = $parsed_files[$file_index] ?? null;
        }

        return $parsed_files;
    }
    
    /**
     * Get client request method
     * 
     * @return string
     */
    public function getMethod()
    {
        return strtolower($this->getServer()->get('request_method'));
    }

    /**
     * Get request attribute
     *
     * @param string $name Attribute name
     * @param mixed $default_value Otherwise value to return if attribute is not found
     * 
     * @return mixed
     */
    public function getAttribute($name, $default_value = null)
    {
        return $this->attributes[$name] ?? $default_value;
    }

    /**
     * Check if input field exists
     *
     * @param string $name Input name
     * 
     * @return bool
     */
    public function hasInput($name)
    {
        return !!$this->input($name);
    }

    /**
     * Get input
     *
     * @param string $name Input name
     * @param string $defaults Default value if input isn't found
     * @return Input
     */
    public function input($name, $defaults = null)
    {
        $names = explode(',', $name);
        
        if(count($names) == 1) {
            $input = $this->inputs()->get($name)->value() ?? $defaults;
            return new Input($input, $name);
        }

        $names = array_map('trim', $names);
        $defaults_vars = explode(',', $defaults);
        $single_default = count($defaults_vars) == 1;
        $inputs = [];

        foreach($names as $index => $rname) {
            $default = $single_default ? $defaults : $defaults_vars[$index];
            $input = $this->inputs()->get($rname)->value() ?? $default;
            $inputs[] = new Input($input, $rname);
        }

        return $inputs;
    }

    /**
     * Get all inputs sent in the request
     * 
     * @return Inputs
     */
    public function inputs()
    {
        if(static::$_input) {
            return static::$_input;
        }

        $content = http_build_query($_POST);
        static::$_input = new Inputs($content);
        return static::$_input;
    }

    /**
     * Add request attributes to space
     * 
     * @param string $name Attribute field name
     * @param mixed[] $value Attribute field value
     * 
     * @return self
     */
    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;
        return $this;
    }

    /**
     * Set custom method
     *
     * @param string $name Method name
     * @param Closure $fn Callable
     * @return self
     */
    public function setCustomMethod($name, $fn)
    {
        $reserved_method_names = array_map('strtolower', get_class_methods($this));

        if(in_array(strtolower($name), $reserved_method_names)) {
            throw new InvalidArgumentException
                ('The specifed method name is a reserved method name');
        }

        $this->_wares[$name] = $fn;
        return $this;
    }

    /**
     * Get this request url
     * 
     * @return \App\Core\Http\Uri
     */
    public function url()
    {
        if(static::$_url) {
            return static::$_url;
        }

        $scheme = $this->getServer()->isHTTPs() ? 'https' : 'http';
        $host = $this->getServer()->get('http_host');
        $uri = $this->getServer()->get('request_uri');

        static::$_url = new Uri($scheme . '://' . $host . $uri);
        return static::$_url;
    }

    /**
     * Undocumented function
     *
     * @param string[] $middleware Middleware name
     *
     * @return self
     * 
     * @throws \InvalidArgumentException
     */
    public function useMiddleware($middlewares)
    {
        if(!count($middlewares)) {
            return $this;
        }

        $wares = App::getConfigByFile('middleware');
        $result = $this;

        foreach($middlewares as $middleware) {

            $class = $wares[$middleware] ?? null;

            if(!$class) {
                throw new \InvalidArgumentException
                    ('Middleware "'.$middleware.'" is not assigned');
            }

            $result = call_user_func_array([new $class, 'trigger'], [$result]);
        }

        return $result;
    }
}