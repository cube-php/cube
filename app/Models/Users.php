<?php

namespace App\Models;

use Cube\Http\Model;

class Users extends Model
{
    protected static $schema = 'users';

    protected static $fields = array(
        'id',
        'created_at',
        'updated_at'
    );
}