<?php

namespace App\Core\Helpers\Cli;

use App\Core\Misc\File;
use App\Core\Exceptions\FileSystemException;
use App\Core\Modules\System;

class CliActions
{
    public static function buildAssetAction($options)
    {
        $name = $options['n'];

        if(!$name) {
            Cli::respondError('No name specified for CSS asset', true);
        }

        return static::buildAsset($options['t'], $name);
    }
    
    public static function buildController($options)
    {
        $name = $options['n'];

        if(!$name) {
            Cli::respondError('No name specified for middleware', true);
        }

        $name = self::getSyntaxedName($name, 'Controller');

        $filename = self::addExt($name);
        $template = self::getReservedTemplate('controller');
        $model_path = APP_CONTROLLERS_PATH . DS . $filename;
        $refined_template = strtr($template, [
            '{className}' => self::getClassName($name),
            '{subNamespace}' => self::getClassNamespace($name)
        ]);

        try {
            Cli::respond('creating controller: ' . $filename);
            $file = new File($model_path, true, true);
            $file->write($refined_template);
            Cli::respondSuccess('created controller: ' . $filename);
        }
        catch(FileSystemException $e) {
            Cli::respondError($e->getMessage());
            Cli::respondError('create controller failed', true);
        }
    }

    public static function buildException($options)
    {
        $name = $options['n'];

        if(!$name) {
            Cli::respondError('No name specified for exception', true);
        }

        $name = self::getSyntaxedName($name, 'Exception');

        $filename = self::addExt($name);
        $template = self::getReservedTemplate('exception');
        $exception_path = APP_EXCEPTIONS_PATH . DS . $filename;
        $refined_template = strtr($template, [
            '{className}' => self::getClassName($name),
            '{subNamespace}' => self::getClassNamespace($name)
        ]);

        try {
            Cli::respond('creating exception: ' . $filename);
            $file = new File($exception_path, true, true);
            $file->write($refined_template);
            Cli::respondSuccess('created exception: ' . $filename);
        }
        catch(FileSystemException $e) {
            Cli::respondError($e->getMessage());
            Cli::respondError('create exception failed', true);
        }
    }

    public static function buildHelp()
    {
        $template = self::getReservedTemplate('help');
        Cli::respond($template, true);
    }

    public static function buildHelper($options)
    {
        $name = $options['n'];

        if(!$name) {
            Cli::respondError('No name specified for helper', true);
        }

        $filename = self::addExt($name);
        $template = self::getReservedTemplate('helper');
        $exception_path = APP_HELPERS_PATH . DS . $filename;
        $refined_template = strtr($template, ['{fn}' => $name]);

        try {
            Cli::respond('creating helper: ' . $filename);
            $file = new File($exception_path, true, true);
            $file->write($refined_template);
            Cli::respondSuccess('created helper: ' . $filename);
        }
        catch(FileSystemException $e) {
            Cli::respondError($e->getMessage());
            Cli::respondError('unable to create helper ' . $filename, true);
        }
    }

    public static function buildMiddleware($options)
    {

        $name = $options['n'];

        if(!$name) {
            Cli::respondError('No name specified for middleware', true);
        }

        $name = self::getSyntaxedName($name, 'Middleware');

        $filename = self::addExt($name);
        $template = self::getReservedTemplate('middleware');
        $middleware_path = APP_MIDDLEWARES_PATH . DS . $filename;
        $refined_template = strtr($template, [
            '{className}' => self::getClassName($name),
            '{subNamespace}' => self::getClassNamespace($name)
        ]);

        try {
            Cli::respond('creating middleware: ' . $filename);
            $file = new File($middleware_path, true, true);
            $file->write($refined_template);
            Cli::respondSuccess('created middleware: ' . $filename);
        }
        catch(FileSystemException $e) {
            Cli::respondError($e->getMessage());
            Cli::respondError(Cli::COMMAND_MIDDLEWARE . ' failed', true);
        }
    }

    public static function buildModel($options)
    {

        $name = $options['n'];

        if(!$name) {
            Cli::respondError('No name specified for model', true);
        }

        $name = self::getSyntaxedName($name, 'Model');

        $filename = self::addExt($name);
        $template = self::getReservedTemplate('model');
        $model_path = APP_MODELS_PATH . DS . $filename;
        $refined_template = strtr($template, [
            '{className}' => self::getClassName($name),
            '{subNamespace}' => self::getClassNamespace($name)
        ]);

        try {
            Cli::respond('creating model: ' . $filename);
            $file = new File($model_path, true, true);
            $file->write($refined_template);
            Cli::respondSuccess('created model: ' . $filename);
        }
        catch(FileSystemException $e) {
            Cli::respondError($e->getMessage());
            Cli::respondError(Cli::COMMAND_MODEL . ' failed', true);
        }
    }

    public static function buildProvider($options)
    {

        $name = $options['n'];

        if(!$name) {
            Cli::respondError('No name specified for provider', true);
        }

        $name = self::getSyntaxedName($name, 'Provider');

        $filename = static::addExt($name);
        $template = static::getReservedTemplate('provider');
        $model_path = APP_PROVIDERS_PATH . DS . $filename;
        $refined_template = strtr($template, [
            '{className}' => static::getClassName($name),
            '{subNamespace}' => static::getClassNamespace($name)
        ]);

        try {
            Cli::respond('creating provider: ' . $filename);
            $file = new File($model_path, true, true);
            $file->write($refined_template);
            Cli::respondSuccess('created provider: ' . $filename);
        }
        catch(FileSystemException $e) {
            Cli::respondError($e->getMessage());
            Cli::respond(Cli::COMMAND_PROVIDER . ' failed', true);
        }
    }

    public static function serve($options)
    {
        $port = $options['p'] ?? 8888;

        if(preg_match("/[^0-9]/", $port)) {
            return Cli::respondError('Invalid port number: ' .$port, true);
        }

        $get_local_ip = function () {
        
            $sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
            socket_connect($sock, "8.8.8.8", 53);
            socket_getsockname($sock, $name);
            
            return $name;
        };

        $keys = array_keys($options);
        $host = in_array('w', $keys) ? $get_local_ip() : '127.0.0.1';
        
        $url = "{$host}:{$port}";
        $dir = APP_WEBROOT_PATH;

        Cli::respondSuccess("Hosting app on: http://{$url}");
        $exec = exec("php -S {$url} -t {$dir}");
        
        if(!$exec) {
            Cli::respondError("Unable to host app on: http://{$url}");
        }
    }

    public static function runSystemCommand($options)
    {
        $system = new System();
        $method_name = $options['n'] ?? null;
        $action = $options['o'] ?? null;

        if(!$method_name) {
            Cli::respondError('No system method specified', true);
        }

        if(!is_callable([$system, $method_name])) {
            return Cli::respondError('Invalid system command', true);
        }

        $raw_args = explode(':', $action);
        $args = array_map('trim', $raw_args);
        return call_user_func_array([$system, $method_name], $args);
    }

    private function addExt($name)
    {
        return "{$name}.php";
    }

    private static function buildAsset($type, $name)
    {
        $allowed_types = ['css', 'js', 'vue', 'svelte', 'sass'];
        $type = strtolower($type);

        if(!in_array($type, $allowed_types)) {
            return Cli::respondError("Unknown asset type {$type}", true);
        }

        $filename = "{$name}.{$type}";
        $template = self::getReservedTemplate('asset');
        $base_path = APP_STORAGE . DS . $type;

        if(!is_dir($base_path)) {
            mkdir($base_path, 0755);
        }

        $model_path = $base_path . DS . $filename;
        $refined_template = strtr($template, [
            '{name}' => $name,
            '{type}' => $type,
            '{date}' => date('jS-M-Y')
        ]);

        try {
            Cli::respond("creating {$type} asset: {$filename}");
            $file = new File($model_path, true, true);
            $file->write($refined_template);
            Cli::respondSuccess("created {$type} asset: {$filename}");
        }
        catch(FileSystemException $e) {
            Cli::respondError($e->getMessage());
            Cli::respondError("Unable to create {$type} asset: {$filename}", true);
        }

        return true;
    }

    private function getClassName($name)
    {
        $name_vars = explode('/', $name);
        $vars_count = count($name_vars);
        $main_name = $name_vars[$vars_count - 1];

        return $main_name;
    }

    private function getClassNamespace($name)
    {
        $name_vars = explode('/', $name);
        $vars_count = count($name_vars);

        if($vars_count == 1) {
            return '';
        }

        echo $vars_count;

        $main_vars = array_slice($name_vars, 0, $vars_count - 1);
        $child_namespace = implode('\\', $main_vars);

        return '\\' . $child_namespace;
    }

    private static function getReservedTemplate($name)
    {
        $path = __DIR__ . DS . '.cli-reserved' . DS . "{$name}.tpl";
        
        try {
            $file = new File($path);
            $content = $file->getContent();
            return $content;
        }
        catch(FileSystemException $e) {
            static::respond($e->getMessage());
            die();
        }
    }

    private static function getSyntaxedName($name, $syntax)
    {
        $name = ucfirst(strtolower($name));
        $syntax_length = strlen($syntax);
        $raw_name = substr($name, 0, -$syntax_length);

        $suffix = substr($name, -$syntax_length, $syntax_length);
        $is_suffix = strtolower($suffix) === strtolower($syntax);

        return $is_suffix ? $raw_name . $syntax : $name . $syntax;
    }
}