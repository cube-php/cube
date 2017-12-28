<?php

namespace App\Core\Http;

use App\Core\Modules\DB;

class Model
{
    /**
     * Model database table name
     * 
     * @var string
     */
    protected static $schema;

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
     * Return all results from schema
     * 
     * @return array|null
     */
    public static function all(array $opts = [])
    {
        $query = DB::table(static::$schema)
                    ->select(static::$fields);

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
     * Fetch all data using passed field value
     *
     * @param string $field Field name
     * @param mixed $value Field value
     * @param array $params Parameters
     * 
     * @return array|null
     */
    public static function findAllBy($field, $value, $params)
    {
        $query = DB::table(static::$schema)
                 ->select(static::$fields)
                 ->where($field, $value);

        return call_user_func_array([$query, 'fetch'], $params);
    }

    /**
     * Fetch data using passed $field value
     * 
     * @param string|int $primary_key
     * 
     * @return array|null
     */
    public static function findBy($field, $value)
    {
        return DB::table(static::$schema)
                ->select(static::$fields)
                ->where($field, $value)
                ->fetchOne();
    }

    /**
     * Fetch data using passed primary key value
     * 
     * @param string|int $primary_key
     * 
     * @return array|null
     */
    public static function findByPrimaryKey($primary_key)
    {
        return DB::table(static::$schema)
                ->select(static::$fields)
                ->where(static::$primary_key, $primary_key)
                ->fetchOne();
    }

    /**
     * Find entry using primary key and delete
     * 
     * @param int|string $primary_key
     * 
     * @return void
     */
    public static function findByPrimaryKeyAndRemove($primary_key)
    {
        $query = DB::table(static::$schema)
                ->delete()
                ->where(static::$primary_key, $primary_key)
                ->fulfil();
    }

    /**
     * Find entry using primary and update entry data
     * 
     * @param string|int $primary_key
     * @param array $update New entry data
     * 
     * @return void
     */
    public static function findByPrimaryKeyAndUpdate($primary_key, array $update)
    {
        $rows = DB::table(static::$schema)
                ->update($update)
                ->where(static::$primary_key, $primary_key)
                ->fulfil();
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