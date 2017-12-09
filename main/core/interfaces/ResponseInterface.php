<?php

namespace App\Core\Interfaces;

interface ResponseInterface
{
    public function withAddedHeader($name, $value);

    public function withHeader($name, $value);

    public function withoutHeader($name);

    public function withStatusCode($code, $reason);

    public function write($string);

    public function writeJson($data);

    public function renderView($path, array $options = []);
}