<?php

namespace App\Models;

use App\Core\Http\Model;

class DemoModel extends Model
{
    protected static $schema = 'schema_name';

    protected static $fields = array(
        'field1',
        'field2'
    );

    protected static $primary_key = 'primary_key_field';
}