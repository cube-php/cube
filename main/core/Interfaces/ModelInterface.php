<?php

namespace App\Core\Interfaces;

interface ModelInterface
{
    public static function all(array $order = [], array $opts = []);
    public static function createEntry(array $entry);
    public static function findAllBy($field, $value, $order = null, $params = null);
    public static function findBy($field, $value);
    public static function findByPrimaryKey($primary_key);
    public static function findByPrimaryKeyAndRemove($primary_key);
    public static function findByPrimaryKeyAndUpdate($primary_key, array $update);
    public static function getCount();
    public static function getCountBy($field, $value);
    public static function getCountQuery();
    public static function getFirst($field = null);
    public static function getLast($field = null);
    public static function query();
    public static function select();
    public static function search($field, $keyword, $limit = null, $offset = null);
}