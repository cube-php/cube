<?php

namespace App\Core\Interfaces;

use App\Core\Modules\FlatDb\FlatDbTable;

interface FlatDbInterface
{
    public function __construct($dir = null);

    public function create($name, array $schema);

    public function get($name) : FlatDbTable;

    public function has($name) : bool;

    public function tables() : array;
}