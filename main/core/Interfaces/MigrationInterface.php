<?php

namespace App\Core\Interfaces;

interface MigrationInterface
{
    public static function up();

    public static function empty();

    public static function down();
}