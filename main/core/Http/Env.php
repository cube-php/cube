<?php

namespace App\Core\Http;

use App\Core\Misc\File;

class Env
{
    
    /**
     * Environment variables
     * 
     * @var string[]
     */
    private static $_vars = array();

    /**
     * Get Environment Variable
     *
     * @param [type] $name
     * @return mixed|null
     */
    public static function get($name)
    {
        static::load();

        $vars = static::$_vars;
        return $vars[$name] ?? null;
    }

    /**
     * Load up all environment variables
     * 
     * @return string[]
     */
    private static function load()
    {
        if(!static::$_vars) {
            $env_file = APP_PATH . DS . '.env';
            
            if(!file_exists($env_file)) {
                $file = new File($env_file, true);
                $file->write('HELLO_WORLD="hello world"');
            }

            if(!static::$_vars) {
                static::$_vars = parse_ini_file($env_file);
            }

            return static::$_vars;
        }
    }
}