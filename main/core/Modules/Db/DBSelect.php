<?php

namespace App\Core\Modules\Db;

use InvalidArgumentException;

use App\Core\Modules\DB;
use App\Core\Modules\Db\DBQueryBuilder;

class DBSelect extends DBQueryBuilder
{       
    /**
     * Fields to select
     * 
     * @var string[]
     */
    private $fields = array();

    /**
     * Table name
     * 
     * @var string
     */
    private $table_name;

    /**
     * Constructor
     * 
     * @param string    $table_name
     * @param array     $fields
     */
    public function __construct($table_name = '', $fields = [])
    {
        if($table_name) {
            $this->joinSql('SELECT', implode(', ', $fields), 'FROM', $table_name);
        }
    }

    /**
     * Fetch content
     * 
     * @param int $offset
     * @param int $limit
     * 
     * @return object|null results
     */
    public function fetch($offset, $limit = null)
    {
        #if limit is not passed,
        #the offset argument should be passed as limit
        $offset_id = (int) ($limit ? (int) $offset : 0);
        $limit_id = (int) ($limit ? $limit : $offset);

        $this->joinSql(
            null,
            'LIMIT',
            $this->addParam($offset_id), ',',
            $this->addParam($limit_id)
        );

        return $this->get();
    }

    /**
     * Fetch all results
     * 
     * @return object|null result
     */
    public function fetchAll()
    {
        return $this->get();
    }

    /**
     * Fetch just one result
     * 
     * @return object|null result
     */
    public function fetchOne()
    {
        $fetched = $this->fetch(0, 1);
        return $fetched ? $fetched[0] : null;
    }

    /**
     * Get first row based on specified field
     *
     * @param string $field Database table field
     * @return object|null
     */
    public function getFirst($field)
    {
        $this->orderByRaw("{$field} DESC");
        return $this->fetchOne();
    }

    /**
     * Get last row based on on specified field
     *
     * @param string $field Database table field
     * @return object|null
     */
    public function getLast($field)
    {
        $this->orderByRaw("{$field} ASC");
        return $this->fetchOne();
    }

    /**
     * Group query
     * 
     * @param string $field Field name
     * 
     * @return self
     */
    public function groupBy($field)
    {
        $this->joinSql('GROUP BY', $field);
        return $this;
    }

    /**
     * Join On query
     * 
     * @param string $field Field name
     * @param string $column_one
     * @param string $operator
     * @param string $column_two
     * 
     * @return self
     */
    public function join($field, $column_one, $operator, $column_two)
    {
        $this->joinSql(null, 'JOIN', $column_one, $operator, $column_two);
    }

    /**
     * Order query
     * 
     * @param array $order
     * 
     * @return self
     */
    public function orderBy(array $orders)
    {

        $orders_list = [];

        foreach($orders as $order) {
            $orders_list[] = $order[0] . ' ' . $order[1];
        }
        
        $this->joinSql(null, 'ORDER BY', implode(', ', $orders_list));
        return $this;
    }

    /**
     * Raw order by query
     * 
     * @param string    $statement Query statement
     * @param string[]  $params
     */
    public function orderByRaw($statement, $params = [])
    {
        $this->joinSql(null, 'ORDER BY', $statement);
        $this->bindParam($params);

        return $this;
    }

    /**
     * Randomize results
     * 
     * @return self
     */
    public function randomize()
    {
        return $this->orderByRaw('RAND()');
    }

    /**
     * Raw select statement
     * 
     * @param string    $table_name Table to select from
     * @param string    $statement  Select statement
     * @param string[]  $params     Statement parameters
     * 
     * @return self
     */
    public function raw($table_name, $statement, $params = []) {

        $this->joinSql('SELECT', $statement, 'FROM', $table_name);

        if(!count($params)) {
            return $this;
        }

        $this->bindParam($params);
        return $this;
    }

    /**
     * Union stateent
     * 
     * @param DBQueryBuilder $query Query
     * 
     * @return self
     */
    public function union(DBQueryBuilder $query)
    {

        $queryToAppend = (string) $query;
        $this->joinSql(null, 'UNION', $queryToAppend);

        return $this;
    }
    
    /**
     * UnionAll statement
     * 
     * @param DBQueryBuilder $query
     * 
     * @return self
     */
    public function unionAll(DBQueryBuilder $query)
    {

        $queryToAppend = (string) $query;
        $this->joinSql(null, 'UNION ALL', $queryToAppend);

        return $this;
    }

    /**
     * Finish query
     * 
     * @return object|null
     */
    private function get()
    {
        $sql = $this->getSqlQuery();
        $params = $this->getSqlParameters();

        $stmt = DB::statement($sql, $params);
        
        if(!$stmt->rowCount()) {
            return null;
        }

        return $stmt->fetchAll();
    }
}