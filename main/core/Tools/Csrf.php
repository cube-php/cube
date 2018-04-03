<?php

namespace App\Core\Tools;

use App\Core\Http\Response;
use App\Core\Http\Request;
use App\Core\Http\Session;

class Csrf
{
    /**
     * Csrf session name
     * 
     * @param string $name
     * 
     * @return string
     */
    private static $_session_name = 'csrf_token_sess';

    /**
     * Generate new token
     * 
     * @return string
     */
    public static function generate()
    {
        return sha1(time()*rand());
    }

    /**
     * Return current csrf_token
     * 
     * @return string
     */
    public static function get()
    {

        $token = Session::get(static::$_session_name);

        if(!$token) {
            $token = static::generate();
            Session::set(static::$_session_name, $token);
        }

        return $token;
    }

    /**
     * Check token validation
     * 
     * @return string
     */
    public static function isValid($token)
    {
        return ((string) $token === static::get());
    }
}