<?php

use App\Core\Http\Request;
use App\Core\App;

/**
 * Return full url based on specified path and query parameters
 *
 * @param string|array $path Path to concantenate with URL
 * @param array $query Query string
 * @return string
 */
function url($path = '', array $query = [])
{
    $request = new Request();

    if(is_array($path)) {
        $path = sprintf('/%s', implode('/', $path));
    }

    $repath = $request->url()->getScheme() . '://' . $request->url()->getHost() . $path;

    return $query ?
        $repath . '?' . http_build_query($query) : $repath;
}

/**
 * Return assets based url
 *
 * @param string $asset_path Asset path
 * @return string
 */
function asset($asset_path)
{
    $full_path = array_merge(['assets'], array_wrap($asset_path));
    return url($full_path);
}

/**
 * Load javascript files
 * 
 * @return string
 */
function jscript($name, $no_cache = null)
{
    if(is_array($name)) {

        $links = '';
        
        foreach($name as $name) {
            $links .= "\n"  . jscript($name, $no_cache);
        }

        return $links;
    }

    $anti_cache = ((is_null($no_cache) || $no_cache) && App::isDevelopment()) ? '?time=' . time() : '';
    return '<script src="' . asset(['js', $name .'.js']) . $anti_cache . '"></script>';
}


/**
 * Load javascript files
 * 
 * @return string
 */
function css($name, $no_cache = null)
{
    if(is_array($name)) {

        $links = '';
        
        foreach($name as $name) {
            $links .= "\n"  . css($name, $no_cache);
        }

        return $links;
    }

    $anti_cache = ((is_null($no_cache) || $no_cache) && App::isDevelopment()) ? '?time=' . time() : '';
    return 
        '<link rel="stylesheet" href="' . asset(['css', $name . '.css']) . $anti_cache . ' "/>';
}