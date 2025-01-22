<?php

namespace Gemvc\Core;

use Gemvc\Database\PdoQuery;
use Gemvc\Http\Response;

class Table extends PdoQuery
{
    private ?string $_query;
    private bool $_isSelectSet;
    private bool $_no_limit;
    /**
     * @var array<mixed> $_binds
     */
    private array $_binds;
    private int $_limit;
    private int $_offset;
    private string $_orderBy;
    private int $_total_count;
    private int $_count_pages;
    /**
     * @var array<string> $_arr_where;
     */
    private array $_arr_where;
    /**
     * @var array<string> $_joins;
     */
    private array $_joins;

    public function __construct()
    {
        $this->_arr_where = [];
        $this->_joins = [];
        $this->_total_count = 0;
        $this->_count_pages = 0;
        $this->_orderBy = '';
        $this->_isSelectSet = false;
        
        if(isset($_ENV['QUERY_LIMIT']) && is_numeric($_ENV['QUERY_LIMIT']))
        {
            $this->_limit = (int)$_ENV['QUERY_LIMIT'];
        }
        else{
            $this->_limit = 10;
        }
        $this->_no_limit = false;
        $this->_offset = 0;
        $this->_query = null;
        $this->_binds = [];
        parent::__construct();
    }

    /**
    * @param array<mixed> $row
    */
    protected function fetchRow(array $row): void
    {
        foreach ($row as $key => $value) {
            $this->$key = $value;
        }
    }

    
    public function getTable(): string
    {
        return '';
    }

    public function getLimit():int  {
        return $this->_limit;
    }
    public function setLimit(int $limit):void
    {
        $this->_limit = $limit;
    }

    public function getQuery(): string|null
    {
        return $this->_query;
    }

    /**
     * @return array<mixed>
     */
    public function getBind(): array
    {
        return $this->_binds;
    }

    public function getCount(): int
    {
        return $this->_count_pages;
    }
    /**
     * return total count of executed Select Query
     * @return int
     */
    public function getTotalCounts(): int
    {
        return $this->_total_count;
    }

    public function setPage(int $page): void
    {
        $page =  $page < 1 ? 0 :  $page - 1 ;
        $this->_offset = ($page - 1) * $this->_limit;
    }

    public function getCurrentPage(): int
    {
        return $this->_offset + 1;
    }

    public function getSelectQueryString(): null|string
    {
        return $this->_query;
    }

    /**
     * @param null|string $columns
     * @return $this
     * if columns = null => SELECT * FROM table ...
     */
    public function select(string $columns = null): self
    {
        if (!$this->_isSelectSet) {
            $this->_query = $columns ? "SELECT $columns " : "SELECT * ";
            $this->_isSelectSet = true;
        } else {
            // If select is called again, append the new columns
            $this->_query .= $columns ? ", $columns" : "";
        }
        return $this;
    }

      /**
     * @param string $table
     * @param string $condition
     * @param string $type
     * @return $this
     */
    public function join(string $table, string $condition, string $type = 'INNER'): self
    {
        $this->_joins[] = strtoupper($type) . " JOIN $table ON $condition";
        return $this;
    }

    /**
    * @param int $limit
    * @return $this
    */
    public function limit(int $limit): self
    {
        $this->_limit = $limit;
        return $this;
    }
    /**
     * remove limit and return all select query
     * @return \Gemvc\Core\Table
     */
    public function noLimit():self
    {
        $this->_no_limit = true;
        return $this;
    }

    /**
     * @param string $column
     * @param mixed $value
     * @return $this
     */
    public function where(string $column, mixed $value): self
    {
        $this->_arr_where[] = count($this->_arr_where) ?  " AND  $column = :$column " : " WHERE $column = :$column ";
        $this->_binds[':' . $column] = $value;
        return $this;
    }

    /**
     * @param string $column
     * @param string $value
     * @return $this
     */
    public function whereLike(string $column, string $value): self
    {
        $this->_arr_where[] = count($this->_arr_where) ? " AND  $column LIKE :$column " : " WHERE $column LIKE :$column ";
        $this->_binds[':' . $column] = $value . '%';
        return $this;
    }

    public function whereLikeLast(string $column, string $value): self
    {
        $this->_arr_where[] = count($this->_arr_where) ? " AND  $column LIKE :$column " : " WHERE $column LIKE :$column ";
        $this->_binds[':' . $column] = '%' . $value;
        return $this;
    }

    public function whereBetween(string $columnName, int|string|float $lowerBand, int|string|float $higherBand): self
    {
        $colLower = ':' . $columnName . 'lowerBand';
        $colHigher = ':' . $columnName . 'higherBand';

        $this->_arr_where[] = count($this->_arr_where) ? " AND  {$columnName} BETWEEN {$colLower} AND {$colHigher} " : " WHERE {$columnName} BETWEEN {$colLower} AND {$colHigher} " ;
        $this->_binds[$colLower] = $lowerBand;
        $this->_binds[$colHigher] = $higherBand;
        return $this;
    }


    /**
     * @param string $column
     * @return $this
     */
    public function whereNull(string $column): self
    {
        $this->_arr_where[] = count($this->_arr_where) ? " AND  $column IS NULL " : " WHERE $column IS NULL ";
        return $this;
    }

    /**
     * @param string $column
     * @return $this
     */
    public function whereNotNull(string $column): self
    {
        $this->_arr_where[] = count($this->_arr_where) ? " AND  $column IS NOT NULL " : " WHERE $column IS NOT NULL ";
        return $this;
    }

    /**
     * @param null|string $columnName
     * @param null|bool $ascending
     * @default columName is id is not given
     * @default order type is Descending if null
     */
    public function orderBy(string $columnName = null, bool $ascending = null): self
    {
        $columnName = $columnName ? $columnName : 'id';
        $ascending = $ascending ? ' ASC ' : ' DESC ';
        $this->_orderBy .=  " ORDER BY  $columnName $ascending ";
        return $this;
    }


    /**
     * @return array<$this>
     */
    public function run(): array
    {
        $objectName= get_class($this);
        if (!$this->_isSelectSet) {
            $this->setError('before any chain function you shall first use select()');
            Response::internalError('before any chain function you shall first use select()')->show();
            die();
        }

        if ($this->getError()) {
            Response::internalError("error in table class for $objectName " . $this->getError())->show();
            die();
        }

        $joinClause = implode(' ', $this->_joins);
        $whereClause = $this->whereMaker();

        $this->_query = $this->_query .
            " , (SELECT COUNT(*) FROM {$this->getTable()} $joinClause $whereClause) AS _total_count " .
            "FROM {$this->getTable()} $joinClause $whereClause ";

        if ($this->_no_limit) {
            $this->_query .= $this->_orderBy . " LIMIT $this->_limit OFFSET $this->_offset ";
        } else {
            $this->_query .= $this->_orderBy;
        }

        $this->_query = trim($this->_query);
        $this->_query = preg_replace('/\s+/', ' ', $this->_query);
        if (!$this->_query) {
            $this->setError('given query-string is not acceptable ');
            Response::internalError("given query-string is not acceptable for $objectName " . $this->getError())->show();
            die();
        }
        $queryResult = $this->selectQuery($this->_query, $this->_binds);
        if (!is_array($queryResult)) {
            Response::internalError("error in table class for $objectName " . $this->getError())->show();
            die();
        }
        if (!count($queryResult)) {
            return [];
        }
        $object_result = [];
        /**@phpstan-ignore-next-line */
        $this->_total_count = (int)$queryResult[0]['_total_count'];
        $this->_count_pages = (int)($this->_total_count / $this->_limit);
        foreach ($queryResult as $item) {
            /**@phpstan-ignore-next-line */
            unset($item['_total_count']);
            $instance = new $this();
            if (is_array($item)) {
                $instance->fetchRow($item);
            }
            $object_result[] = $instance;
        }
        return $object_result;
    }

    private function whereMaker(): string
    {
        if (!count($this->_arr_where)) {
            return ' WHERE 1 ';
        }
        $query = ' ';
        foreach ($this->_arr_where as $value) {
            $query .= ' ' . $value . ' ';
        }
        $query = trim($query);
        return $query;
    }
}
