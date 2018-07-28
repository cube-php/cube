<?php

/**
 * HTML functions should go here
 */

function h($element_name, $attributes = [], $innerhtml = '') {
    $self_closing = array(
        'area',
        'base',
        'br',
        'col',
        'command',
        'embed',
        'hr',
        'img',
        'input',
        'keygen',
        'link',
        'menuitem',
        'meta',
        'param',
        'source',
        'track',
        'wbr'
    );

    $element_name = strtolower($element_name);
    $element = "<{$element_name} ";
    $attrs = [];

    if($attributes) {
        foreach($attributes as $name => $value) {
            $attrs[] = $name . '="' . $value .'"';
        }
    }

    $joined_attr = implode(' ', $attrs);
    $element .= $joined_attr;

    if(in_array($element, $self_closing)) {
        return $element .= '/>';
    }

    return $element .= ">{$innerhtml}</{$element_name}>";
}