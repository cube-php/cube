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
     * Server
     * 
     * @var \App\Core\Http\Server
     */
    private $_server;

    /**
     * Header
     * 
     * @var \App\Core\Http\Headers
     */
    private $_headers;

    /**
     * Middlewares
     * 
     * @var string]
     */
    public $_wares = array();

    /**
     * Class constructor
     * 
     */
    public function __construct()
    {
        $this->_server = new Server;
        $this->_headers = new Headers;
    }

    /**
     * Call middlewares
     * 
     * @param string $method Method name
     * @param string[] $args Method arguments
     * 
     * @return void
     */
    public function __call($method, $args)
    {
        $ware = array_key_exists($method, $this->_wares);

        if(!$ware) {
            throw new InvalidArgumentException
                ('Middleware "'. $method .'" not found');
        }
        
        return $this->_wares[$method];
    }

    /**
     * Getter
     * 
     * @param string $name Getter name
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
     * @return \App\Core\Http\Headers
     */
    public function getHeaders()
    {
        return $this->_headers;
    }

    /**
     * Return request server variables
     *
     * @return App\Core\Http\Server;
     */
    public function getServer()
    {
        return $this->_server;
    }

    /**
     * Get uploaded files
     * 
     * @return \App\Core\Http\UploadedFile
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
        return strtolower($this->_server->get('request_method'));
    }

    /**
     * Get request attribute
     * 
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
     * @return bool
     */
    public function hasInput($name)
    {
        return !!$this->input($name);
    }

    /**
     * Get input
     * 
     * @return \App\Core\Misc\Input
     */
    public function input($name)
    {
        return $this->inputs()->get($name);
    }

    /**
     * Get all inputs sent in the request
     * 
     * @return App\Core\Misc\Input
     */
    public function inputs()
    {
        $inputs = new Inputs;
        return $inputs;
    }

    /**
     * Add request attributes to space
     * 
     * @param string $name Attribute field name
     * @param mixed[] $value Atrribute field value
     * 
     * @return self
     */
    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    /**
     * Get this request url
     * 
     * @return \App\Core\Http\Uri
     */
    public function url()
    {
        $scheme = $this->_server->get('request_scheme');
        $host = $this->_server->get('http_host');
        $uri = $this->_server->get('request_uri');

        return new Uri($scheme . '://' . $host . $uri);
    }

    /**
     * Undocumented function
     *
     * @return void
     * 
     * @throws \InvalidArgumentException
     */
    public function useMiddleware($middleware)
    {
        $wares = App::getConfigByFile('middleware');
        $class = $wares[$middleware] ?? null;

        if(!$class) {
            throw new \InvalidArgumentException
                ('Middleware "'.$middleware.'" is not assigned');
        }

        $cls = new $class;
        $this->_wares[$middleware] = call_user_func_array([$cls, 'handle'], [$this, new Response]);
        return $this;
    }
}