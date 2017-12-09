<?php

use App\Core\Http\Request;
use App\Core\App;

/**
 * Url
 */
function url($path = '')
{
    $request = new Request();
    return $request->url()->getScheme() . '://' . $request->url()->getHost() . '/' . $path;
}

/**
 * Load javascript files
 * 
 * @return string
 */
function jscript($name, $no_cache = null)
{
    $anti_cache = ((is_null($no_cache) || $no_cache) && App::isDevelopment()) ? '?time=' . time() : '';
    return '<script src="' . url('assets/js/'. $name .'.js') . $anti_cache . '"></script>' . PHP_EOL;
}


/**
 * Load javascript files
 * 
 * @return string
 */
function css($name, $no_cache = null)
{
    $anti_cache = ((is_null($no_cache) || $no_cache) && App::isDevelopment()) ? '?time=' . time() : '';
    return 
        '<link rel="stylesheet" href="' . url('assets/css/'. $name .'.css') . $anti_cache . ' "/>' . PHP_EOL;
}