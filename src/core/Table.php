<?php

namespace GemFramework\Core;
use GemLibrary\Database\PdoQuery;
use GemLibrary\Helper\StringHelper;

class Table extends PdoQuery
{

    private int    $pagination_limit;
    private string $page;
    private null|int    $count;
    private string $find;
    private string $between;
    private string $orderBy;
    private string $listQuery;
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
        parent::__construct();
    }

    public function setTable(): string
    {
        return '';
    }

    public function setPage(int $page): void
    {
        $this->page = " LIMIT {$this->pagination_limit} OFFSET  $page";
    }

    public function setFind(string $find): void
    {
        $res = explode(':', $find);
        $find_item =  $res[0];
        $find_value = StringHelper::sanitizedString($res[1]);
        if (property_exists($this, $find_item)) {
            $this->find = " AND {$find_item} LIKE :list_find_value";
            $this->listBindValues['list_find_value'] = $find_value . "%";
        }
    }

    public function setBetween(string $between): void
    {
        $res = explode(':', $between);
        $between_item =  $res[0];
        $res_between = explode('-', $res[1]);
        $between_low = StringHelper::sanitizedString($res_between[0]);
        $between_high = StringHelper::sanitizedString($res_between[1]);
        if (property_exists($this, $between_item)) {
            $this->between = " AND {$between_item} BETWEEN :list_between_low AND :list_between_high";
            $this->listBindValues[':list_between_low'] = $between_low;
            $this->listBindValues[':list_between_high'] = $between_high;
        }
        $this->between = $between;
    }

    /**
     * @param string $orderBy
     * @example $orderBy = 'id:asc'
     */
    public function setOrderBy(string $orderBy): void
    {
        $res = explode(':', $orderBy);
        $order_item =  $res[0];

        if (isset($res[1])) {
            if ($res[1] == 'asc') {
                $this->sort = 'ASC';
            }
        }
        if (property_exists($this, $order_item)) {
            $this->orderBy = " ORDER BY {$order_item} {$this->sort} ";
        }
    }

    public function setWhere(string $where): void
    {
        $res = explode(':', $where);
        $where_item =  $res[0];
        $where_value = StringHelper::sanitizedString($res[1]);
        if (property_exists($this, $where_item)) {
            $this->where = " AND WHERE {$where_item} = :list_where_value";
            $this->listBindValues['list_where_value'] = $where_value;
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

    
    public function getListBindValues(): array/**@phpstan-ignore-line */
    {
        return $this->listBindValues;
    }


    /**
     * @return array<mixed>|false
     */
    public function ListQuery(): array|false
    {
        $this->listQuery = "SELECT * FROM {$this->setTable()} WHERE deleted_at IS NULL  {$this->where} {$this->find} {$this->between} {$this->orderBy} {$this->page}";
        $countQuery = "SELECT COUNT(*) FROM {$this->setTable()} WHERE deleted_at IS NULL  {$this->where} {$this->find} {$this->between}";
        $count = $this->selectQuery($countQuery, $this->listBindValues);
        $this->count = $count[0]['COUNT(*)'];/**@phpstan-ignore-line */
        return $this->selectQueryObjets($this->listQuery, $this->listBindValues);
    }

    /**
     * @return array<mixed>|false
     */
    public function ListDeactivesQuery(): array|false
    {
        $this->listQuery = "SELECT * FROM {$this->setTable()} WHERE is_active = 0 {$this->find} {$this->between} {$this->orderBy} {$this->page}";
        $countQuery = "SELECT COUNT(*) FROM {$this->setTable()} WHERE is_active = 0 {$this->find} {$this->between}";
        $count = $this->selectQuery($countQuery, $this->listBindValues);
        $this->count = $count[0]['COUNT(*)'];/**@phpstan-ignore-line */
        return $this->selectQueryObjets($this->listQuery, $this->listBindValues);
    }

    /**
     * @return array<mixed>|false
    */
    public function ListTrashQuery(): array|false
    {
        $this->listQuery = "SELECT * FROM {$this->setTable()} WHERE deleted_at IS NOT NULL {$this->find} {$this->between} {$this->orderBy} {$this->page}";
        $countQuery = "SELECT COUNT(*) FROM {$this->setTable()} WHERE deleted_at IS NOT NULL {$this->find} {$this->between}";
        $count = $this->selectQuery($countQuery, $this->listBindValues);
        $this->count = $count[0]['COUNT(*)'];/**@phpstan-ignore-line */
        return $this->selectQueryObjets($this->listQuery, $this->listBindValues);
    }

    /**
     * @param int $id
     * @return object|false
     */
    public function selectById(int $id): object|false
    {
        $found = $this->selectQueryObjets("SELECT * FROM {$this->setTable()} WHERE id = :id LIMIT 1",[':id'=>$id]);
        if(isset($found[0]))
        {
            /** @phpstan-ignore-next-line */
            return $found[0];
        }
        return false;
    }
}
