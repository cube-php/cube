<?php

namespace App\Core\Http;

use App\Core\Modules\DB;
use App\Core\Modules\Db\DBTable;
use App\Core\Modules\Db\DBSelect;
use App\Core\Interfaces\ModelInterface;
use App\Core\Exceptions\ModelException;
use App\Core\Modules\Db\DBUpdate;
use App\Core\Modules\Db\DBDelete;

class Model implements ModelInterface
{
    /**
     * Model database table name
     * 
     * @var string
     */
    protected static $schema;

    /**
     * Model provider
     *
     * @var string|null
     */
    protected static $provider = null;

    /**
     * Selectable fields from specified $schema
     * 
     * @var array
     */
    protected static $fields = array();

    /**
     * Primary key field name
     * 
     * @var string
     */
    protected static $primary_key;

    /**
     * Model data
     *
     * @var object
     */
    private $_data;

    /**
     * Model updates
     *
     * @var array
     */
    private $_updates = array();

    /**
     * Model constructor
     *
     * @param object $id
     */
    public function __construct(object $data)
    {
        $this->_data = $data;
    }

    /**
     * Getter
     *
     * @param [type] $name
     * @return void
     */
    public function __get($name)
    {
        return $this->_data->{$name} ?? null;
    }

    /**
     * Add an update
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $this->_updates[$name] = $value;
    }

    /**
     * Save updates
     *
     * @return bool
     */
    public function save(): bool
    {
        return !!static::update($this->_updates)
                    ->where(self::$primary_key, $this->id)
                    ->fulfil();
    }

    /**
     * Return all results from schema
     *
     * @param array $order Order methods
     * @see \App\Core\Db\DBSelect::orderBY() method
     * 
     * @param array $opts
     * @param array|null $fields Field to select
     * @param string|null $map Provider class to map to model
     * @return array|null
     */
    public static function all(?array $order = null, ?array $opts = null, ?array $fields = null, ?string $map = null)
    {
        $query = static::select($fields)
                    ->orderBy($order);

        return $opts ? 
            call_user_func_array([$query, 'fetch'], $opts) : $query->fetchAll();
    }

    /**
     * Insert new entry into schema
     * 
     * @param array $entry Data to store
     * 
     * @return int Insert id
     */
    public static function createEntry(array $entry)
    {
        return DB::table(static::$schema)->insert($entry);
    }

    /**
     * Delete from schema
     *
     * @return DBDelete
     */
    public static function delete(): DBDelete
    {
        return DB::table(static::$schema)->delete();
    }

    /**
     * Fetch all data using passed field value
     *
     * @param string $field Field name
     * @param mixed $value Field value
     * @param array|null $order Order methods
     * @param array|null $params Parameters
     * @param array $fields Fields to retrieve
     * @return array
     */
    public static function findAllBy($field, $value, $order = null, $params = null, array $fields = [])
    {
        static::checkField($field);

        $query = static::select($fields)
                 ->where($field, $value)
                 ->orderBy($order);

        if(!$params) {
            return call_user_func([$query, 'fetchAll']);
        }

        return call_user_func_array([$query, 'fetch'], $params);
    }

    /**
     * Fetch data using passed $field value
     * 
     * @param string $field Field name
     * @param mixed $value Field value
     * @param mixed $fields Fields to retrieve
     * 
     * @return object|null
     */
    public static function findBy($field, $value, array $fields = [])
    {

        static::checkField($field);

        return static::select($fields)
                ->where($field, $value)
                ->fetchOne();
    }

    /**
     * Fetch data using passed primary key value
     * 
     * @param string|int $primary_key
     * @param array $fields Fields to retrieve
     * 
     * @return object|null
     */
    public static function findByPrimaryKey($primary_key, array $fields = [])
    {
        return static::select($fields)
                ->where(static::getPrimaryKey(), $primary_key)
                ->fetchOne();
    }

    /**
     * Find entry using primary key and delete
     * 
     * @param int|string $primary_key
     * 
     * @return int
     */
    public static function findByPrimaryKeyAndRemove($primary_key)
    {
        return DB::table(static::$schema)
                ->delete()
                ->where(static::getPrimaryKey(), $primary_key)
                ->fulfil();
    }

    /**
     * Find entry using primary and update entry data
     * 
     * @param string|int $primary_key
     * @param array $update New entry data
     * 
     * @return int
     */
    public static function findByPrimaryKeyAndUpdate($primary_key, array $update)
    {
        return DB::table(static::$schema)
                ->update($update)
                ->where(static::getPrimaryKey(), $primary_key)
                ->fulfil();
    }

    /**
     * Fetch
     *
     * @param integer $count
     * @return self[]
     */
    public static function fetch(int $count)
    {
        return static::select()->fetch($count);
    }

    /**
     * Return the number of rows on table
     *
     * @return int
     */
    public static function getCount()
    {
        $key = static::getPrimaryKey();
        return DB::table(static::$schema)
                ->select(['count('. $key .') tcount'])
                ->fetchOne()
                ->tcount;
    }

    /**
     * Return the number of rows on table based on specified field and value
     *
     * @param string $field Schema column name
     * @param mixed $value Value
     * @return int
     */
    public static function getCountBy($field, $value)
    {
        static::checkField($field);
        $key = static::getPrimaryKey();

        return DB::table(static::$schema)
                ->select(['count('. $key .') tcount'])
                ->where($field, $value)
                ->fetchOne()
                ->tcount;
    }

    /**
     * Return a raw query-able count query
     *
     * @return DBSelect
     */
    public static function getCountQuery()
    {
        $key = static::getPrimaryKey();
        return DB::table(static::$schema)
                ->select(["count({$key}) as count"]);
    }

    /**
     * Get first entry based on specified field
     * Or primary key if field is not specified
     *
     * @param string|null $field
     * @param array $fields Fields to retrieve
     * @return object|null
     */
    public static function getFirst($field = null, array $fields = [])
    {

        static::checkField($field);

        return static::select($fields)
                ->getFirst(($field ?? static::getPrimaryKey()));
    }

    /**
     * Get last entry based on specified field
     * Or primary key if field is not specified
     *
     * @param string|null $field
     * @param array $fields
     * @return object|null
     */
    public static function getLast($field = null, array $fields = [])
    {
        static::checkField($field);

        return static::select($fields)
                ->getLast(($field ?? static::getPrimaryKey()));
    }

    /**
     * Schema's primary key
     *
     * @return string
     */
    public static function getPrimaryKey()
    {
        return static::$primary_key;
    }

    /**
     * Sum schema by field
     *
     * @param string $field
     * @return float Total sum
     */
    public static function getSumByField(string $field)
    {
        static::checkField($field);
        return DB::table(static::$schema)->sum($field);
    }

    /**
     * Query schema's table directly
     *
     * @return DBTable
     */
    public static function query(): DBTable
    {
        return DB::table(static::$schema);
    }

    /**
     * Update table rows
     *
     * @param array $fields
     * @return DBUpdate
     */
    public static function update(array $fields)
    {
        return self::query()->update($fields);
    }

    /**
     * Update field where there is a matching data or create new entry if $fields does not match
     *
     * @param array $fields
     * @param array $data
     * @return int
     */
    public static function updateOrCreate(array $fields, array $data)
    {
        $query = self::update($data);

        array_walk($fields, function ($value, $name) use ($query) {
            $query->where($name, $value);
        });

        $rows = $query->fulfil();
        
        if(!$rows) {
           $new_data = array_merge($fields, $data);
            return self::createEntry($new_data);
        }

        return $rows;
    }

    /**
     * Run custom queries on model's schema
     *
     * @param array|string $fields Fields to override default fields
     * @return DBSelect
     */
    public static function select($fields = null): DBSelect
    {
        $field_list = is_array($fields) ? $fields : [$fields];
        $select = new DBSelect(
            static::$schema,
            $fields ? $field_list :
            static::$fields,
            get_called_class()
        );

        return $select;
    }

    /**
     * Search for matching fields
     *
     * @param string $field Field to search
     * @param int|null $offset Offset
     * @param int|null $limit Limit
     * @param array $fields Fields to retrieve
     * @return object[]|null
     */
    public static function search($field, $keyword, $limit = null, $offset = null, array $fields = [])
    {

        static::checkField($field);

        $query = static::select($fields)
                ->whereLike($field, $keyword);

        if(!$limit) {
            return $query->fetchAll();
        }

        $offset = $offset ?? 0;
        return $query->fetch($offset, $limit);
    }

    /**
     * Check field if it's declared
     *
     * @param string $field Field name
     * @return bool
     * @throws ModelException
     */
    private static function checkField(string $field)
    {

        if(!in_array($field, static::$fields)) {
            throw new ModelException('Trying to access undeclared field "'. $field .'"');
        }

        return true;
    }

    /**
     * Parse fields to readable
     * 
     * @return string
     */
    private static function fields()
    {
        $fields = static::$fields;

        if(!count($fields)) return '*';

        return implode(', ', $fields);
    }
}