<?php

namespace App\Core\Interfaces;

interface InputInterface
{

    public function __toString();

    public function isEmail();

    public function isEmpty();

    public function isInt();

    public function isRegex();

    public function isUrl();

    public function matches($regex);

}