<?php

namespace App\Core\Modules\Db;

use App\Core\Modules\Db\DBTable;

use App\Core\Modules\Db\DBSchemaBuilder;

class DBTableBuilder
{
    

    /**
     * Table name
     * 
     * @var \App\Core\Modules\DB\DBTable
     */
    private $table;

    /**
     * DBTableBuilder constructor
     * 
     * @param \App\Core\Modules\DB\DBTable $table
     */
    public function __construct(DBTable $table)
    {
        $this->table = $table;
    }

    /**
     * Add field to table
     * 
     * @return \App\Core\Modules\DB\DBSchemaBuilder
     */
    public function field($name)
    {
        return new DBSchemaBuilder($this->table, $name);
    }
}