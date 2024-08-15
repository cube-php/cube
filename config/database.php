<?php

/**
 * ----------------------------------------
 * Cube database configuration
 * ----------------------------------------
 * Set all database settings
 */

return array(

    'mysql' => array(
        /** @var string Database Driver */
        'driver' => env('db_driver'),

        /** @var string Database host name */
        'hostname' => env('db_hostname'),

        /** @var string Database username */
        'username' => env('db_username'),

        /** @var string Database password */
        'password' => env('db_password'),

        /** @var string Database name */
        'dbname' => env('db_name'),

        /** @var string Database prot */
        'port' => env('db_port'),

        /** @var string Database character set */
        'charset' => env('db_charset')
    )
);
