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

    public static function get($name)
    {
        $variables = static::load();
        return $variables[$name] ?? null;
    }

    /**
     * Load up all enviroment variables
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

            static::$_vars = parse_ini_file($env_file);
            return static::$_vars;
        }
    }
}