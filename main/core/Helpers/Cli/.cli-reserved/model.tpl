<?php

namespace App\Models{subNamespace};

use App\Core\Http\Model;

class {className} extends Model
{
    protected static $schema = '{tableName}';
    
    protected static $primary_key = 'id';

    protected static $fields = array(
        'created_at',
        'updated_at'
    );
}