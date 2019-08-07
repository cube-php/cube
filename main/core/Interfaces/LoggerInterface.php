<?php

namespace App\Core\Interfaces;

interface LoggerInterface
{
    public function get();

    public function set(string $content);
}