<?php

/**
 * Array functions here
 * 
 * ==============================================
 * Arrays focused methods will and should go here
 * ==============================================
 */

 /**
  * Get the first element of an array
  *
  * @param array $arr
  * @return void
  */
function array_get_first(array $arr) {
    return $arr[0];
}

/**
 * Get the last element of an array
 *
 * @param array $arr
 * @return mixed
 */
function array_get_last(array $arr) {
    $index = count($arr) - 1;
    return $arr[$index];
}

/**
 * Shuffle array
 *
 * @param array $arr
 * @return array
 */
function array_shuffle(array $arr) {
    shuffle($arr);
    return $arr;
}

/**
 * Wrap value as array if not an array
 * Else return the value itself
 *
 * @param mixed $var
 * @return array
 */
function array_wrap($var) {
    if(!is_array($var)) {
        return [$var];
    }
    return $var;
}