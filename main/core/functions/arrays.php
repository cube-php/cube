<?php

/**
 * Array functions here
 * 
 * ==============================================
 * Arrays focused methods will and should go here
 * ==============================================
 */

/**
 * Wrap value as array if not an array
 * Else return the value itself
 *
 * @param mixed $var
 * @return array
 */
function array_wrap($var) : array
{
    if(!is_array($var)) {
        return [$var];
    }
    return $var;
}