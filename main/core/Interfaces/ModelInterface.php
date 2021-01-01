<?php

namespace Cube\Interfaces;

use Cube\Modules\Db\DBDelete;
use Cube\Modules\Db\DBSelect;
use Cube\Modules\Db\DBTable;

interface ModelInterface
{
    public static function all(?array $order = null, ?array $opts = null, ?array $fields = null, ?string $map = null);
    public static function createEntry(array $entry);
    public static function delete(): DBDelete;
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
    public static function query(): DBTable;
    public static function select(?array $fields = null): DBSelect;
    public static function search($field, $keyword, $limit = null, $offset = null, array $fields = []);
}