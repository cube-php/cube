<?php

use Cube\Http\Request;
use Cube\App;

/**
 * Return full url based on specified path and query parameters
 *
 * @param string|array $path Path to concantenate with URL
 * @param null|array $query Query string
 * @return string
 */
function url($path = '', ?array $query = null) : string
{
    $request = new Request();

    if(is_array($path)) {
        $path = sprintf('/%s', implode('/', $path));
    }

    $repath = $request->url()->getHostName() . $path;

    return $query ?
        $repath . '?' . http_build_query($query) : $repath;
}

/**
 * Return assets based url
 *
 * @param string $asset_path Asset path
 * @param bool $should_cache
 * @return string
 */
function asset($asset_path, bool $should_cache = false) : string
{
    $full_path = array_merge(['assets'], array_wrap($asset_path));
    $query = $should_cache ? ['v' => asset_token()] : null;
    return url($full_path, $query);
}

/**
 * Load javascript files
 * 
 * @return string
 */
function jscript($name, $no_cache = null) : string
{
    if(is_array($name)) {

        $links = '';
        
        foreach($name as $name) {
            $links .= "\n"  . jscript($name, $no_cache);
        }

        return $links;
    }

    $asset = asset(['js', $name . '.js'], true);

    return h('script', ['src' => $asset]);
}


/**
 * Load javascript files
 * 
 * @return string
 */
function css($name, $no_cache = null) : string
{
    if(is_array($name)) {

        $links = '';
        
        foreach($name as $name) {
            $links .= "\n"  . css($name, $no_cache);
        }

        return $links;
    }

    $asset = asset(['css', $name . '.css'], true);

    return h('link', [
        'rel' => 'stylesheet',
        'href' => $asset
    ]);
}

function asset_token() : string {
    if(App::isDevelopment()) {
        return time();
    }

    return md5(env('ASSET_VERSION'));
}