<?php

namespace App\Core\Helpers\Cli;

use App\Core\Misc\File;
use App\Core\Exceptions\FileSystemException;

class Cli
{

    private const COMMAND_MODEL = '--makeModel';
    private const COMMAND_PROVIDER = '--makeProvider';
    private const COMMAND_CONTROLLER = '--makeController';
    private const COMMAND_HELPER = '--makeHelper';
    private const COMMAND_STORAGE = '--storage';

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
    private static $_allowed_args = array(
        '--makeModel',
        '--makeProvider',
        '--makeController',
        '--makeHelper',
        '--makeView',
        '--help',
        '--storage',
        '--css',
        '--js'
    );
    
    /**
     * Constructor
     *
     * @param string[] $args
     */
    public function __construct($args)
    {
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
            static::respond('No command sent!', true);
        }

        foreach($this->_args as $arg)
        {
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

        if(!in_array($mainCommand, static::$_allowed_args)) {
            static::respond("\033[31m Invalid command \"{$mainCommand}\"");
            return false;
        }

        return [
            'command'  => $mainCommand,
            'act' => $mainCommandArgs[1] ?? null
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
                $this->buildController($action);
                break;

            case static::COMMAND_MODEL:
                $this->buildModel($action);
                break;

            case static::COMMAND_PROVIDER:
                $this->buildProvider($action);
                break;
        }
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
        $refined_template = strtr($template, ['{className}' => $name]);

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
        $refined_template = strtr($template, ['{className}' => $name]);

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
        $refined_template = strtr($template, ['{className}' => $name]);

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
     * Return reserved template
     *
     * @param string $action_type
     * 
     * @return void
     */
    private function getReservedTemplate($action_type)
    {
        $path = CORE_APP_PATH . DS . '.cli-reserved' . DS . "{$action_type}.tpl";
        
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
    public function addExt($name)
    {
        return "{$name}.php";
    }

    /**
     * CLI Response renderer
     *
     * @param [type] $msg
     * @return string
     */
    private static function respond($msg, $kill = false)
    {
        echo 'php-cube: ' . $msg . PHP_EOL;
        if($kill) {
            die();
        }
    }
}