<?php

namespace App\Core\Misc;

use InvalidArgumentException;

class EventManager
{
    /**
     * Registered events
     * 
     * @var array
     */
    private static $events = array();

    /**
     * Class constructor
     * 
     */
    public function __construct() {}

    /**
     * Make events callable statically
     * 
     * @param string $name Event name
     * @param array $args
     * 
     * @return void
     */
    public static function __callStatic($name, $args)
    {

        if(count($args) > 1) {
            throw new InvalidArgumentException
                ($name . ' should contain just 1 argument which is the callback function');
        }

        $callback = $args[0] ?? null;

        if(!is_callable($callback)) {
            throw new InvalidArgumentException
                ($name . '\'s argument should is not a valid callback function');
        }

        return static::on($name, $callback);
    }

    /**
     * Run all functions called on event
     * 
     * @return void
     */
    public static function dispatchEvent($handler, $arg = null)
    {
        if(!static::hasAttachedEvents($handler)) return false;

        $handles = static::$events[$handler];

        foreach($handles as $function)
        {
            $function($arg);
        }
    }

    /**
     * Check if handler has events
     * 
     * @return bool
     */
    public static function hasAttachedEvents($handler)
    {
        return (
            isset(static::$events[$handler]) &&
            is_array(static::$events[$handler])
        );
    }

    /**
     * Add new event listener
     * 
     * @param int|string $handler Event Handler
     * @param callable $callback Callback function
     * 
     * @return void
     */
    public static function on($handler, callable $callback)
    {
        if(!isset(static::$events[$handler])) static::$events[$handler] = array();
        static::$events[$handler][] = $callback;
    }
}