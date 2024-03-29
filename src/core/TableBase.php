<?php

namespace GemFramework\Core;

use GemLibrary\Database\PdoQuery;
use GemLibrary\Helper\StringHelper;

class TableBase extends PdoQuery
{
    public int          $is_active;
    public ?string      $deleted_at;
    private int         $pagination_limit;
    private string      $page;
    private int         $count;
    private string      $find;
    private string      $between;
    private string      $orderBy;
    private string      $listQuery;
    private ?string     $parent_key;
    /**
     * @var array<mixed>
     */
    private array  $listBindValues;
    private string $sort;
    private string $where;

    public function __construct()
    {
        $this->pagination_limit = $_ENV['PAGINATION_LIMIT'];
        $this->page = " LIMIT {$this->pagination_limit} OFFSET 0";
        $this->count = 0;
        $this->sort = 'DESC';
        $this->find = '';
        $this->between = '';
        $this->orderBy = "ORDER BY id DESC";
        $this->listBindValues = [];
        $this->where = '';
        $this->listQuery = "";
        $this->parent_key = '';

        parent::__construct();
    }

    public function setTable(): string
    {
        return '';
    }

    /**
     * @param int $page
     */
    public function setPage(int $page): void
    {
        $offset = $page * $this->pagination_limit;
        $this->page = " LIMIT {$this->pagination_limit} OFFSET $offset";
    }

    /**
     * @param string $column
     * @param mixed $find_value
     * @example setFind('first_name','Pet')
     */
    public function setFind(string $column , mixed $find_value): void
    {
        if (property_exists($this, $column)) {
            $this->find = " AND {$column} LIKE :list_find_value";
            $this->listBindValues['list_find_value'] = $find_value . "%";
        }
    }

    /**
     * @param string $between
     * @example setBetween('created_at', '2008' , '2020')
     */
    public function setBetween(string $column , int|float|string $lower_band , int|float|string $higher_band): void
    {   
        $this->between = " AND {$column} BETWEEN :list_between_low AND :list_between_high";
        $this->listBindValues[':list_between_low'] = $lower_band;
        $this->listBindValues[':list_between_high'] = $higher_band;
    }

    /**
     * @param string $orderBy
     * @param bool   $desc 
     * @example setOrderBy('last_name')
     */
    public function setOrderBy(string $orderBy, bool $desc = true): void
    {
        $this->sort = $desc ? 'DESC' : 'ASC';
        if (property_exists($this, $orderBy)) {
            $this->orderBy = " ORDER BY {$orderBy} {$this->sort} ";
        }
    }

    /**
     * @param string $where
     * @example setWhere('city_id',583)
     */
    public function setWhere(string $whereColumn , mixed $value): void
    {
        if (property_exists($this, $whereColumn)) {
            $this->where = " AND $whereColumn = :{$whereColumn}";
            $this->listBindValues[":{$whereColumn}"] = $value;
        }
    }


    public function getCount(): null|int
    {
        return $this->count;
    }

    public function getListQuery(): string
    {
        return $this->listQuery;
    }

    /**
     * @return array<mixed>
     */
    public function getListBindValues(): array
    {
        return $this->listBindValues;
    }

    /**
     * @param string $parentKey
     * @param int    $parentValue
     * it will affect query on listQuery() to select rows where parent key = value
     */
    public function setParentKey(string $parentKey, int $parentValue): void
    {
        $this->parent_key = " AND WHERE {$parentKey} = :parent_key_id ";
        $this->listBindValues[':parent_key_id'] = $parentValue;
    }

    /**
     * @return array<mixed>|false
     */
    public function ListQuery(): array|false
    {

        $this->listQuery = "SELECT * FROM {$this->setTable()} WHERE deleted_at IS NULL {$this->parent_key} {$this->where} {$this->find} {$this->between} {$this->orderBy} {$this->page}";
        $countQuery = "SELECT COUNT(*) FROM {$this->setTable()} WHERE deleted_at IS NULL {$this->parent_key} {$this->where} {$this->find} {$this->between}";
        $count = $this->selectCountQuery($countQuery, $this->listBindValues);
        if ($count !== false) {
            $this->count = $count;
            return $this->selectQueryObjets($this->listQuery, $this->listBindValues);
        }
        return false;
    }

    /**
     * @return array<mixed>|false
     */
    public function ListDeactivesQuery(): array|false
    {

        $this->listQuery = "SELECT * FROM {$this->setTable()} WHERE is_active = 0 {$this->parent_key} {$this->where} {$this->find} {$this->between} {$this->orderBy} {$this->page}";
        $countQuery = "SELECT COUNT(*) FROM {$this->setTable()} WHERE is_active = 0 {$this->parent_key} {$this->where} {$this->find} {$this->between}";
        $count = $this->selectCountQuery($countQuery, $this->listBindValues);
        if ($count !== false) {
            $this->count = $count;
            return $this->selectQueryObjets($this->listQuery, $this->listBindValues);
        }
        return false;
    }

    /**
     * @return array<mixed>|false
     */
    public function ListActivesQuery(): array|false
    {

        $this->listQuery = "SELECT * FROM {$this->setTable()} WHERE is_active = 1 {$this->parent_key} {$this->find} {$this->between} {$this->orderBy} {$this->page}";
        $countQuery = "SELECT COUNT(*) FROM {$this->setTable()} WHERE is_active = 1 {$this->parent_key} {$this->find} {$this->between}";
        $count = $this->selectCountQuery($countQuery, $this->listBindValues);
        if ($count !== false) {
            $this->count = $count;
            return $this->selectQueryObjets($this->listQuery, $this->listBindValues);
        }
        return false;
    }

    /**
     * @return array<mixed>|false
     */
    public function ListTrashQuery(): array|false
    {
        $this->listQuery = "SELECT * FROM {$this->setTable()} WHERE deleted_at IS NOT NULL {$this->parent_key} {$this->where} {$this->where} {$this->find} {$this->between} {$this->orderBy} {$this->page}";

        $countQuery = "SELECT COUNT(*) FROM {$this->setTable()} WHERE deleted_at IS NOT NULL  {$this->parent_key} {$this->where} {$this->where} {$this->find} {$this->between}";
        $count = $this->selectCountQuery($countQuery, $this->listBindValues);
        if ($count !== false) {
            $this->count = $count;
            return $this->selectQueryObjets($this->listQuery, $this->listBindValues);
        }
        return false;
    }

    /**
     * @param int $id
     * @return object|false|null
     */
    public function selectById(int $id): object|false|null
    {
        $found = $this->selectQueryObjets("SELECT * FROM {$this->setTable()} WHERE id = :id LIMIT 1", [':id' => $id]);
        if ($found === false) {
            return false;
        }
        if (count($found) === 0) {
            return null;
        }
        /** @phpstan-ignore-next-line */
        return $found[0];
    }
}
