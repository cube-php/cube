<?php

namespace App\Core\Modules\Db;

use App\Core\Modules\Db\DBTable;
use App\Core\Modules\Db\DBSchemaBuilder;

class DBTableBuilder
{
    

    /**
     * Table name
     * 
     * @var DBTable
     */
    private $table;

    /**
     * DBTableBuilder constructor
     * 
     * @param DBTable $table
     */
    public function __construct(DBTable $table)
    {
        $this->table = $table;
        
        $this->field('created_at')->datetime();
        $this->field('updated_at')->datetime();
    }

    /**
     * Add field to table
     * 
     * @return DBSchemaBuilder
     */
    public function field($name)
    {
        return new DBSchemaBuilder($this->table, $name);
    }
}