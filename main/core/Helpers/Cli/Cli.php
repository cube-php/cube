<?php

namespace App\Core\Helpers\Cli;

use App\Core\Misc\File;

class Cli
{

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
    private $_allowed_args = array(
        'makeModel',
        'makeProvider',
        'makeController',
        'makeHelper'
    );
    
    public function __construct($args)
    {
        $this->_args = array_splice($args, 1);
    }

    public function listen()
    {
        var_dump($this->_args);
    }
}