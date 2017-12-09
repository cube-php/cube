<?php

namespace App\Core\Router;

use InvalidArgumentException;

use App\Core\Router\Route;

use App\Core\Router\RouteCollection;

class RouteParser
{

    /**
     * Route regexp matcher
     * 
     * @var array
     */
    private static $regex = array(
        '*int' => '([0-9]+)',
        '*string' => '([\w]+)',
        '*bool' => '(true|false)',
        '*any' => '([^\/]+)'
    );

    /**
     * Route
     * 
     * @var \App\Core\Tools\Route
     */
    private $_route;

    /**
     * Class constructor
     * 
     * @param \App\Core\Tools\Route $route
     */
    public function __construct(Route $route)
    {
        $this->_route = $route;
    }

    /**
     * Parse route path and generate regular expression
     * 
     * @return string
     */
    public function regexp()
    {
        $regexp_path = '';
        $rawpath = $this->_route->getPath();

        $path = $this->compileRegularPath($rawpath);
        $path = $this->compileStrictPathParams($path);

        #Enforce trailing slash
        $path = (substr($path, -1, 1) === '/') ? $path : $path . '/';

        return $path;
    }
    
    /**
     * Compile regular path conditions
     * 
     * @param string $path Path to compile
     * 
     * @return string Compiled path
     */
    private function compileRegularPath($path)
    {

        #check for regular path conditions
        $regular_path = preg_match_all('#(\{(.*?)\})#', $path, $matches);
        
        if(!$regular_path) return $path;

        $newpath = $path;

        foreach($matches[1] as $index => $match)
        {
            #remove brackets
            $regexps = static::$regex;
            $match_without_brackets = str_replace(['{','}'], '', $match);
            $match_vars = explode(':', $match_without_brackets);
            $match_vars_count = count($match_vars);
            $isRegularPath = preg_match('/\:/', $match);

            if(!$isRegularPath) {
                continue;
            }

            if($match_vars_count < 2 || $match_vars_count > 2) {
                throw new InvalidArgumentException('Invalid route path for route "' . $path . '"');
            }

            #get parameter values
            $parameter_name = $match_vars[1];
            $parameter_raw_value = trim($match_vars[0]);
            $parameter_value = '';

            #Specify attribute's index to route
            $this->_route->setAttribute($parameter_name);

            if(array_key_exists($parameter_raw_value, $regexps)){
                $parameter_value = $regexps[$parameter_raw_value];
            }

            if(!$parameter_value) {
                $parameter_value = '(' . $parameter_raw_value . ')';
            }

            $newpath = str_replace($match, $parameter_value, $newpath);
        }

        return $newpath;
    }

    /**
     * Compile strict parameters path
     * 
     * @param string $path
     * 
     * @return string
     */
    private function compileStrictPathParams($path)
    {
        #check for strict parameters
        $strict_parameters = preg_match_all('#\{([^\/]+)\}#', $path, $matches);

        if(!$strict_parameters) return $path;

        $newpath = $path;

        foreach($matches[0] as $index => $match)
        {

            $parameter_name = str_replace(['{', '}'], '', $match);
            $this->_route->setAttribute($parameter_name);
            $newpath = str_replace($match, static::$regex['*any'], $newpath);
        }

        return $newpath;
    }
}