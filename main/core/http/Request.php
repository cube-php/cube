<?php

namespace App\Core\Http;

use App\Core\Interfaces\RequestInterface;

use App\Core\Http\Server;

use App\Core\Http\Session;

use App\Core\Http\Uri;

use App\Core\Misc\FilesParser;

use App\Core\Misc\Inputs;
use function GuzzleHttp\json_decode;

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
    private $server;

    /**
     * Middlewares
     * 
     * @var string]
     */
    public static $_wares = array();

    /**
     * Class constructor
     * 
     */
    public function __construct()
    {
        $this->server = new Server;
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
        return static::$_wares[$method];
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
     * Add middlewarw
     * 
     * @param string $name
     * @param        $data
     */
    public function addMiddleWare($name, $data)
    {
        static::$_wares[$name] = $data;
    }

    /**
     * Get request body
     *
     * @param boolean $parsed Return JSON encoded body or not
     * @return object
     */
    public function getBody($parsed = true)
    {
        $contents = file_get_contents('php://input');
        return $parsed ? 
            json_decode($contents) : $contents;
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
        return strtolower($this->server->get('request_method'));
    }

    /**
     * Get request attribute
     * 
     * @return
     */
    public function getAttribute($name)
    {
        return $this->attributes[$name] ?? null;
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
        $scheme = $this->server->get('request_scheme');
        $host = $this->server->get('http_host');
        $uri = $this->server->get('request_uri');

        return new Uri($scheme . '://' . $host . $uri);
    }
}