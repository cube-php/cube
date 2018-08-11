<?php

/**
 * --------------------------
 * CUBE VIEW CONFIG
 * ---------------------------
 * This file should contain your
 * custom filters and functions
 * that you want available in views
 */

return array(
    
    /**
     * Functions
     */
    'functions' => array(
        'var_dump'
    ),

    /**
     * Filters
     */
    'filters' => array(
        'hello' => function ($str) {
            return 'Hello world: ' . $str;
        }
    )
);