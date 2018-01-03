<?php

namespace App\Core\Interfaces;

interface RequestInterface
{
    public function getMethod();

    public function getAttribute($name);

    public function inputs();

    public function input($name);

    public function setAttribute($name, $value);

    public function url();
}