<?php

namespace App\Core\Interfaces;

interface ModelInterface
{
    public static function all(array $order = [], array $opts = []);
    public static function createEntry(array $entry);
    public static function findAllBy($field, $value, $order = null, $params = null);
    public static function findBy($field, $value, array $fields = []);
    public static function findByPrimaryKey($primary_key, array $fields = []);
    public static function findByPrimaryKeyAndRemove($primary_key);
    public static function findByPrimaryKeyAndUpdate($primary_key, array $update);
    public static function getCount();
    public static function getCountBy($field, $value);
    public static function getCountQuery();
    public static function getFirst($field = null, array $fields = []);
    public static function getLast($field = null, array $fields = []);
    public static function query();
    public static function select(array $fields = []);
    public static function search($field, $keyword, $limit = null, $offset = null, array $fields = []);
}