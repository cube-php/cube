<?php

namespace App\Core\Helpers\Cli;

use App\Core\Misc\File;
use App\Core\Modules\System;
use App\Core\Exceptions\FileSystemException;

class Cli
{
    const COMMAND_MODEL      = 'make:model';
    const COMMAND_PROVIDER   = 'make:provider';
    const COMMAND_CONTROLLER = 'make:controller';
    const COMMAND_HELPER     = 'make:helper';
    const COMMAND_EXCEPTION  = 'make:exception';
    const COMMAND_MIDDLEWARE = 'make:middleware';
    const COMMAND_ASSET        = 'make:asset';
    const COMMAND_HELP       = 'help';
    const COMMAND_SYSTEM     = 'system';
    const COMMAND_SERVE      = 'serve';

    const OUTPUT_TEXT        = 'text';
    const OUTPUT_ERROR       = 'error';
    const OUTPUT_SUCCESS     = 'success';

    public function __construct()
    {
    }

    public function listen()
    {
        $options = getopt(self::shortOpts(), self::longOpts());
        return $this->runCommand($options);
    }

    /**
     * Get command sent
     *
     * @param array $options
     * @return mixed
     */
    private function getCommand($options)
    {
        $sent_commands = array_keys($options);
        $system_commands = self::longOpts();

        $difference = array_intersect($sent_commands, $system_commands);
        $count = count($difference);

        if($count > 1) {
            $commands = implode(', ', $difference);
            return self::respondError
                ('Two commands "' . $commands . '" cannot be run at the same time, Run a specific command at once', true);
        }

        return $difference[0];
    }

    /**
     * Get arguments
     *
     * @param array $options
     * @return array
     */
    private function getArgs($options)
    {
        $sent_commands = array_keys($options);
        $system_commands = self::longOpts();

        $difference = array_diff($sent_commands, $system_commands);
        $datas = [];

        foreach($difference as $item) {
            $datas[$item] = $options[$item];
        }

        return $datas;
    }

    private function runCommand($options)
    {
        $command = $this->getCommand($options);
        $args = $this->getArgs($options);

        switch($command)
        {
            case self::COMMAND_SERVE:
                return CliActions::serve($args);
                break;

            case self::COMMAND_PROVIDER:
                return CliActions::buildProvider($args);
                break;

            case self::COMMAND_MIDDLEWARE:
                return CliActions::buildMiddleware($args);
                break;

            case self::COMMAND_MODEL:
                return CliActions::buildModel($args);
                break;

            case self::COMMAND_HELPER:
                return CliActions::buildHelper($args);
                break;

            case self::COMMAND_HELP:
                return CliActions::buildHelp();
                break;

            case self::COMMAND_EXCEPTION:
                return CliActions::buildException($args);
                break;

            case self::COMMAND_CONTROLLER:
                return CliActions::buildController($args);
                break;

            case self::COMMAND_ASSET:
                return CliActions::buildAssetAction($args);
                break;

            case self::COMMAND_SYSTEM:
                return CliActions::runSystemCommand($args);
                break;

            default:
                return CliActions::buildHelp();
                break;
        }
    }
    
    /**
     * CLI Response renderer
     *
     * @param [type] $msg
     * @return string
     */
    public static function respond($msg, $kill = false, $type = self::OUTPUT_TEXT)
    {
        $color = self::getOutputColor($type);
        $text[] = $color ? "\e[" . $color['text_color'] : '';
        $text[] = $color ?  $color['bg_color'] . 'm' : '';
        $text[] = $msg;
        $text[] = "\e[0m";
        $text[] = PHP_EOL;

        echo implode($text);
        if($kill) die();
    }

    public static function respondError($msg, $kill = false)
    {
        return self::respond($msg, $kill, self::OUTPUT_ERROR);
    }

    public static function respondSuccess($msg, $kill = false)
    {
        return self::respond('** ' . $msg . ' **', $kill, self::OUTPUT_SUCCESS);
    }

    private static function longOpts()
    {
        return array(
            self::COMMAND_MODEL,
            self::COMMAND_PROVIDER,
            self::COMMAND_CONTROLLER,
            self::COMMAND_HELPER,
            self::COMMAND_EXCEPTION,
            self::COMMAND_MIDDLEWARE,
            self::COMMAND_ASSET,
            self::COMMAND_HELP,
            self::COMMAND_SYSTEM,
            self::COMMAND_SERVE
        );
    }

    private static function shortOpts()
    {
        $commands = [
            'p:',
            'h:',
            'n:',
            't:',
            'o:',
            'l',
            'w'
        ];

        return implode('', $commands);
    }
    
    private static function getOutputColor(string $type)
    {
        $colors = array(
            self::OUTPUT_ERROR => ['bg_color' => 41, 'text_color' => '1;37;'],
            self::OUTPUT_SUCCESS => ['bg_color' => 40, 'text_color' => '0;32;'],
            //self::OUTPUT_TEXT => ['bg_color' => 40, 'text_color' => '1;37;']
        );

        $selected = $colors[$type] ?? null;
        return $selected;
    }
}