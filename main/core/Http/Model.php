<?php

namespace App\Core\Http;

use App\Core\Modules\DB;
use App\Core\Modules\Db\DBTable;
use App\Core\Modules\Db\DBSelect;
use App\Core\Modules\Db\DBQueryBuilder;

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
     * @param array $order Order methods
     * @see \App\Core\Db\DBSelect::orderBY() method
     * 
     * @param array $opts
     * @return array|null
     */
    public static function all(array $order = [], array $opts = [])
    {
        $query = DB::table(static::$schema)
                    ->select(static::$fields)
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
     * Fetch all data using passed field value
     *
     * @param string $field Field name
     * @param mixed $value Field value
     * @param array|null $order Order methods
     * @param array|null $params Parameters
     * @return array
     */
    public static function findAllBy($field, $value, $order = null, $params = null)
    {
        $query = DB::table(static::$schema)
                 ->select(static::$fields)
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

        return $query;
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

        return $rows;
    }

    /**
     * Return the number of rows on table
     *
     * @return int
     */
    public static function getCount()
    {
        $key = static::$primary_key;
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
        $key = static::$primary_key;
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
        $key = static::$primary_key;
        return DB::table(static::$schema)
                ->select(["count({$key}) as count"]);
    }

    /**
     * Get first entry based on specified field
     * Or primary key if field is not specified
     *
     * @param string|null $field
     * @return object|null
     */
    public static function getFirst($field = null)
    {
        return DB::table(static::$schema)
                ->select(static::$fields)
                ->getFirst(($field ?? static::$primary_key));
    }

    /**
     * Get last entry based on specified field
     * Or primary key if field is not specified
     *
     * @param string|null $field
     * @return object|null
     */
    public static function getLast($field = null)
    {
        return DB::table(static::$schema)
                ->select(static::$fields)
                ->getLast(($field ?? static::$primary_key));
    }

    /**
     * Query schema's table directly
     *
     * @return DBTable
     */
    public static function query()
    {
        return DB::table(static::$schema);
    }

    /**
     * Run custom queries on model's schema
     *
     * @return DBSelect|DBQueryBuilder
     */
    public static function select()
    {
        return new DBSelect(static::$schema, static::$fields);
    }

    /**
     * Search for matching fields
     *
     * @param string $field Field to search
     * @param int|null $offset Offset
     * @param int|null $limit Limit
     * @return object[]|null
     */
    public static function search($field, $keyword, $limit = null, $offset = null)
    {
        $query = DB::table(static::$schema)
                ->select(static::$fields)
                ->whereLike($field, $keyword);

        if(!$limit) {
            return $query->fetchAll();
        }

        $offset = $offset ?? 0;
        return $query->fetch($offset, $limit);
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