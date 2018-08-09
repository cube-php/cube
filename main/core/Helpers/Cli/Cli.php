<?php

namespace App\Core\Helpers\Cli;

use App\Core\Misc\File;
use App\Core\Modules\System;
use App\Core\Exceptions\FileSystemException;

class Cli
{

    const COMMAND_MODEL      = '--makeModel';
    const COMMAND_PROVIDER   = '--makeProvider';
    const COMMAND_CONTROLLER = '--makeController';
    const COMMAND_HELPER     = '--makeHelper';
    const COMMAND_EXCEPTION  = '--makeException';
    const COMMAND_MIDDLEWARE = '--makeMiddleware';
    const COMMAND_CSS        = '--css';
    const COMMAND_JSCRIPT    = '--js';
    const COMMAND_HELP       = '--help';
    const COMMAND_SYSTEM     = '--system';
    const COMMAND_SERVE      = '--serve';

    /**
     * Reserved templates directory
     *
     * @var string
     */
    private $_default_templates = '.reserved';

    /**
     * Arguments
     *
     * @var string[]
     */
    private $_args;

    /**
     * Allowed arguments
     *
     * @var string[]
     */
    private static $_allowed_args = [];

    /**
     * Cube's default project serve host
     *
     * @var string
     */
    private $_default_serve_host = '127.0.0.1';

    /**
     * Cube's default project port url
     *
     * @var string
     */
    private $_default_serve_port = '8888';
    
    /**
     * Constructor
     *
     */
    public function __construct()
    {

        $args = $_SERVER['argv'];

        static::$_allowed_args = array(
            self::COMMAND_MODEL,
            self::COMMAND_CONTROLLER,
            self::COMMAND_EXCEPTION,
            self::COMMAND_HELPER,
            self::COMMAND_PROVIDER,
            self::COMMAND_CSS,
            self::COMMAND_JSCRIPT,
            self::COMMAND_HELP,
            self::COMMAND_MIDDLEWARE,
            self::COMMAND_SYSTEM,
            self::COMMAND_SERVE
        );

        foreach (array_slice($args, 1) as $arg) {
            $this->_args[] = $arg;
        }
    }

    /**
     * Listen to CLI commands
     *
     * @return void
     */
    public function listen()
    {
        if(!$this->_args) {
            static::respond('No command sent!');
            return $this->buildHelp();
        }

        foreach($this->_args as $arg) {
            
            $command_pool = $this->isValidCommand($arg);
            
            if($command_pool) {
                $this->runCommand($command_pool['command'], $command_pool['act']);
                continue;
            }
            break;
        }
    }

    /**
     * Check if command is a valid command
     *
     * @param string $command
     * @return boolean|array
     */
    private function isValidCommand($command)
    {
        $mainCommandArgs = explode(':', $command);
        $mainCommand = $mainCommandArgs[0];
        $commandArgs = (count($mainCommandArgs) == 2) ?
            ($mainCommandArgs[1] ?? null) : (array_slice($mainCommandArgs, 1) ?? []);

        if(!in_array($mainCommand, static::$_allowed_args)) {
            static::respond("\033[31m Invalid command \"{$mainCommand}\"");
            $this->buildHelp();
            return false;
        }

        return [
            'command'  => $mainCommand,
            'act' => $commandArgs
        ];
    }

    /**
     * CLI Command runner
     *
     * @param [type] $command
     * @param [type] $action
     * @return void
     */
    private function runCommand($command, $action)
    {
        switch($command)
        {
            case static::COMMAND_CONTROLLER:
                return $this->buildController($action);
                break;

            case static::COMMAND_MODEL:
                return $this->buildModel($action);
                break;

            case static::COMMAND_PROVIDER:
                return $this->buildProvider($action);
                break;

            case self::COMMAND_EXCEPTION:
                return $this->buildException($action);
                break;

            case self::COMMAND_MIDDLEWARE:
                return $this->buildMiddleware($action);
                break;

            case self::COMMAND_CSS:
                return $this->buildAssetCss($action);
                break;

            case self::COMMAND_JSCRIPT:
                return $this->buildAssetJs($action);
                break;

            case self::COMMAND_HELPER:
                return $this->buildHelper($action);
                break;

            case self::COMMAND_HELP:
                return $this->buildHelp();
                break;

            case self::COMMAND_SYSTEM:
                return $this->runSystemCommand($action);
                break;

            case self::COMMAND_SERVE:
                return $this->serve($action);
                break;

            default:
                self::respond('Invalid command sent!');
                return $this->buildHelp();
                break;
        }
    }

    /**
     * CLI Build asset
     *
     * @param string $name
     * @return void
     */
    private function buildAsset($type, $name)
    {
        $allowed_types = ['css', 'js'];
        $type = strtolower($type);

        if(!in_array($type, $allowed_types)) {
            return self::respond("Unknown asset type {$type}", true);
        }

        $filename = "{$name}.{$type}";
        $template = $this->getReservedTemplate('asset');
        $model_path = APP_PUBLIC_STORAGE_PATH . DS . $type . DS . $filename;
        $refined_template = strtr($template, [
            '{name}' => $name,
            '{type}' => $type,
            '{date}' => date('jS-M-Y')
        ]);

        try {
            static::respond("creating {$type} asset: {$filename}");
            $file = new File($model_path, true, true);
            $file->write($refined_template);
            static::respond("created {$type} asset: {$filename}");
        }
        catch(FileSystemException $e) {
            static::respond($e->getMessage());
            static::respond("Unable to create {$type} asset: {$filename}", true);
        }

        return true;
    }

    /**
     * CLI Build css asset
     *
     * @param string $name
     * @return void
     */
    private function buildAssetCss($name)
    {
        return $this->buildAsset('css', $name);
    }

    /**
     * CLI Build js asset
     *
     * @param string $name
     * @return void
     */
    private function buildAssetJs($name)
    {
        return $this->buildAsset('js', $name);
    }

    /**
     * CLI Build controller
     *
     * @param string $name
     * @return void
     */
    private function buildController($name)
    {
        $filename = $this->addExt($name);
        $template = $this->getReservedTemplate('controller');
        $model_path = APP_CONTROLLERS_PATH . DS . $filename;
        $refined_template = strtr($template, [
            '{className}' => $this->getClassName($name),
            '{subNamespace}' => $this->getClassNamespace($name)
        ]);

        try {
            static::respond('creating controller: ' . $filename);
            $file = new File($model_path, true, true);
            $file->write($refined_template);
            static::respond('created controller: ' . $filename);
        }
        catch(FileSystemException $e) {
            static::respond($e->getMessage());
            static::respond('create controller failed', true);
        }
    }

    /**
     * CLI Build exception
     *
     * @param string $name
     * @return void
     */
    private function buildException($name)
    {
        $filename = $this->addExt($name);
        $template = $this->getReservedTemplate('exception');
        $exception_path = APP_EXCEPTIONS_PATH . DS . $filename;
        $refined_template = strtr($template, [
            '{className}' => $this->getClassName($name),
            '{subNamespace}' => $this->getClassNamespace($name)
        ]);

        try {
            static::respond('creating exception: ' . $filename);
            $file = new File($exception_path, true, true);
            $file->write($refined_template);
            static::respond('created exception: ' . $filename);
        }
        catch(FileSystemException $e) {
            static::respond($e->getMessage());
            static::respond('create exception failed', true);
        }
    }

    /**
     * CLI Build help
     *
     * @param string $name
     * @return void
     */
    private function buildHelp()
    {
        $template = $this->getReservedTemplate('help');
        static::respond($template, true);
    }

    /**
     * CLI Build helper
     *
     * @param string $name
     * @return void
     */
    private function buildHelper($name)
    {
        $filename = $this->addExt($name);
        $template = $this->getReservedTemplate('helper');
        $exception_path = APP_HELPERS_PATH . DS . $filename;
        $refined_template = strtr($template, ['{fn}' => $name]);

        try {
            static::respond('creating helper: ' . $filename);
            $file = new File($exception_path, true, true);
            $file->write($refined_template);
            static::respond('created helper: ' . $filename);
        }
        catch(FileSystemException $e) {
            static::respond($e->getMessage());
            static::respond('unable to create helper ' . $filename, true);
        }
    }

    /**
     * CLI Model builder
     *
     * @param string $name
     * @return void
     */
    private function buildModel($name)
    {
        $filename = $this->addExt($name);
        $template = $this->getReservedTemplate('model');
        $model_path = APP_MODELS_PATH . DS . $filename;
        $refined_template = strtr($template, [
            '{className}' => $this->getClassName($name),
            '{subNamespace}' => $this->getClassNamespace($name)
        ]);

        try {
            static::respond('creating model: ' . $filename);
            $file = new File($model_path, true, true);
            $file->write($refined_template);
            static::respond('created model: ' . $filename);
        }
        catch(FileSystemException $e) {
            static::respond($e->getMessage());
            static::respond('create model failed', true);
        }
    }

    /**
     * CLI Model builder
     *
     * @param string $name
     * @return void
     */
    private function buildMiddleware($name)
    {
        $filename = $this->addExt($name);
        $template = $this->getReservedTemplate('middleware');
        $middleware_path = APP_MIDDLEWARES_PATH . DS . $filename;
        $refined_template = strtr($template, [
            '{className}' => $this->getClassName($name),
            '{subNamespace}' => $this->getClassNamespace($name)
        ]);

        try {
            static::respond('creating middleware: ' . $filename);
            $file = new File($middleware_path, true, true);
            $file->write($refined_template);
            static::respond('created middleware: ' . $filename);
        }
        catch(FileSystemException $e) {
            static::respond($e->getMessage());
            static::respond('create middleware failed', true);
        }
    }

    /**
     * CLI Provider builder
     *
     * @param string $name
     * @return void
     */
    private function buildProvider($name)
    {
        $filename = $this->addExt($name);
        $template = $this->getReservedTemplate('provider');
        $model_path = APP_PROVIDERS_PATH . DS . $filename;
        $refined_template = strtr($template, [
            '{className}' => $this->getClassName($name),
            '{subNamespace}' => $this->getClassNamespace($name)
        ]);

        try {
            static::respond('creating provider: ' . $filename);
            $file = new File($model_path, true, true);
            $file->write($refined_template);
            static::respond('created provider: ' . $filename);
        }
        catch(FileSystemException $e) {
            static::respond($e->getMessage());
            static::respond('create provider failed', true);
        }
    }

    /**
     * Get file name paths
     *
     * @param string $name
     * @return object
     */
    private function getClassName($name)
    {
        $name_vars = explode('/', $name);
        $vars_count = count($name_vars);
        $main_name = $name_vars[$vars_count - 1];

        return $main_name;
    }

    /**
     * Retrieve class namespaces
     *
     * @param string $name
     * @return string
     */
    public function getClassNamespace($name)
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

    /**
     * Return reserved template
     *
     * @param string $action_type
     * 
     * @return void
     */
    private function getReservedTemplate($action_type)
    {
        $path = __DIR__ . DS . '.cli-reserved' . DS . "{$action_type}.tpl";
        
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

    /**
     * Add extension to filename
     *
     * @param string $name
     * @return string
     */
    private function addExt($name)
    {
        return "{$name}.php";
    }

    /**
     * Executes system command logic
     *
     * @param string $action
     * @return self
     */
    private function runSystemCommand($action)
    {
        $system = new System;
        $method_name = is_array($action) ? $action[0] : $action;

        if(!is_callable([$system, $method_name])) {
            return static::respond('Invalid system command', true);
        }

        $args = is_array($action) ? array_slice($action, 1) : [$action];

        return call_user_func_array([$system, $method_name], $args);
    }

    /**
     * Serve project
     *
     * @param string $addr Server Address
     * @return bool
     */
    private function serve($addr_args)
    {
        $addr_args = is_array($addr_args) ? $addr_args : [$addr_args];
        $count = count($addr_args);

        if($count > 2) {
            return self::respond('Invalid address and port', true);
        }

        $host = $addr_args[0] ?? $this->_default_serve_host;
        $port = $addr_args[1] ?? $this->_default_serve_port;
        $serve_addr = $host . ':' . $port;

        self::respond('Php cube server now running on ' . $serve_addr);
        
        $dir = APP_WEBROOT_PATH;
        return exec("php -S {$serve_addr} -t {$dir}");
    }

    /**
     * CLI Response renderer
     *
     * @param [type] $msg
     * @return string
     */
    public static function respond($msg, $kill = false)
    {
        echo "php-cube: {$msg} ". PHP_EOL;
        if($kill) die();
    }
}