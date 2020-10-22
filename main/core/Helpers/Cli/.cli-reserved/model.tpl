<?php

namespace App\Models{subNamespace};

use App\Core\Http\Model;

class {className} extends Model
{
    protected static $schema = '{schema_name}';
    
    protected static $primary_key = '{primary_key_field}';

    protected static $provider = null;

    protected static $fields = array(
        'created_at',
        'updated_at'
    );
}