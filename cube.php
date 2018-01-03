<?php

require 'vendor/autoload.php';

use App\Core\Helpers\Cli\Cli;

$args = $argv;

$cli = new Cli($args);
$cli->listen();