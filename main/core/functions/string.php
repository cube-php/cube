<?php

/**
 * Concatenate string
 *
 * @param ...$args Arguments
 * @return string
 */
function concat(...$args): string {
    return implode($args);
}

/**
 * String starts with
 *
 * @param string $needle
 * @param string $haystack
 * @return boolean
 */
function str_starts_with($needle, $haystack): bool {
    return substr($haystack, 0, 1) === $needle;
}

/**
 * String ends with
 *
 * @param string $needle
 * @param string $haystack
 * @return boolean
 */
function str_ends_with($needle, $haystack): bool {
    return substr($haystack, -1, 1) === $needle;
}