<?php

namespace App\Core\Http;

use App\Core\Http\Cookie;
use App\Core\Misc\Collection;
use App\Core\Modules\SessionManager;

class Session
{
    /**
     * Session name
     *
     * @var string
     */
    private static $_cookie_name = 'CUBESESSID';

    /**
     * Session instance
     *
     * @var self
     */
    private static $_instance = null;

    /**
     * Session constructor
     * 
     * 
     */
    private function __construct()
    {
        Cookie::set(static::$_cookie_name, '');
        /**
         * Create session manager instance
         */
        $handler = new SessionManager();

        /**
         * Set custom session handlers
         */
        session_set_save_handler(
            array($handler, 'open'),
            array($handler, 'close'),
            array($handler, 'read'),
            array($handler, 'write'),
            array($handler, 'destroy'),
            array($handler, 'gc')
        );

        register_shutdown_function('session_write_close');

        /**
         * Set session name
         */
        session_name(static::$_cookie_name);

        /**
         * Start session
         */
        session_start();
        session_regenerate_id();
    }

    /**
     * Creates and starts session
     *
     * @return void
     */
    public static function createInstance()
    {
        if(!static::$_instance) {
            static::$_instance = new self;
        }

        return static::$_instance;
    }

    /**
     * Check if session exists
     *
     * @param string $name Session name 
     * 
     * @return boolean
     */
    public static function has($name)
    {
        return isset($_SESSION[$name]);
    }

    /**
     * Get session value
     * 
     * @param string $name Session name
     * 
     * @return mixed|null
     */
    public static function get($name)
    {
        if(!static::has($name)) {
            return null;
        }

        return $_SESSION[$name];
    }

    /**
     * Returns session name
     * 
     * @return string
     */
    public static function name() {
        return session_name();
    }

    /**
     * Regenerate session name
     * 
     * @return string New session id
     */
    public static function regenerate()
    {
        session_regenerate_id();
        return session_id();
    }

    /**
     * Remove session
     *
     * @param string $name Session name
     * 
     * @return bool
     */
    public static function remove($name)
    {
        if(!static::has($name)) {
            return false;
        }

        unset($_SESSION[$name]);
        return true;
    }

    /**
     * Set new session
     *
     * @param string $name Session name
     * @param string $value Session value
     * 
     * @return bool
     */
    public static function set($name, $value)
    {
        $_SESSION[$name] = $value;
        return true;
    }
}