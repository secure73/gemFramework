<?php

namespace GemFramework\Core;

use GemLibrary\Database\PdoQuery;
use GemLibrary\Helper\StringHelper;
use GemFramework\Traits\Table\ActivateQueryTrait;
use GemFramework\Traits\Table\DeactivateQueryTrait;
use GemFramework\Traits\Table\InsertSingleQueryTrait;
use GemFramework\Traits\Table\RemoveQueryTrait;
use GemFramework\Traits\Table\SafeDeleteQueryTrait;
use GemFramework\Traits\Table\SelectQueryTrait;
use GemFramework\Traits\Table\UpdateQueryTrait;
class Table extends PdoQuery
{
    use SelectQueryTrait;
    use InsertSingleQueryTrait;
    use RemoveQueryTrait;
    use UpdateQueryTrait;
    use SafeDeleteQueryTrait;
    use ActivateQueryTrait;
    use DeactivateQueryTrait;

    public int          $is_active;
    public ?string      $deleted_at;
    private int         $pagination_limit;
    private string      $page;
    private int         $count;
    private string      $find;
    private string      $between;
    private string      $orderBy;
    private string      $listQuery;
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

    /**
     * @param int $id
     * @return null|self
     * if found, set item values to this object and return this object
     */
    public function single(int $id):null|self
    {
        $res = $this->selectQuery("SELECT * FROM {$this->setTable()} WHERE id = :id LIMIT 1",[':id'=>$id]);
        if($res === false)
        {
            $this->setError('Failed to select row from table:'.$this->getError());
            return null;
        }
        if(count($res) == 0)
        {
            $this->setError('No row found with id:'.$id);
            return null;
        }
        $res = $res[0];
        /*@phpstan-ignore-next-line*/
        foreach ($res as $key => $value) {
            $this->$key = $value;
        }
        return $this;
    }

    public function setPage(int $page): void
    {
        $offset = $page * $this->pagination_limit;
        $this->page = " LIMIT {$this->pagination_limit} OFFSET $offset";
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
            $this->where = " AND {$where_item} = :{$where_item}";
            $this->listBindValues[":{$where_item}"] = $where_value;
        }
    }

    public function getCount(): null|int
    {
        return $this->count;
    }

    public function getListQuerystring(): string
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
     * @param string|null $forignKey
     * @param mixed|null $forign_key_value
     * @return array<mixed>|false
     */
    public function ListQuery(string $forignKey = null , mixed $forign_key_value = null): array|false
    {
        $whereForignKey = '';
        if($forignKey)
        {
            $whereForignKey = " AND WHERE {$forignKey} = :key_id";
            $this->listBindValues[':key_id'] = $forign_key_value;
        }
        $this->listQuery = "SELECT * FROM {$this->setTable()} WHERE deleted_at IS NULL {$whereForignKey} {$this->where} {$this->find} {$this->between} {$this->orderBy} {$this->page}";
        $countQuery = "SELECT COUNT(*) FROM {$this->setTable()} WHERE deleted_at IS NULL {$whereForignKey} {$this->where} {$this->find} {$this->between}";
        $count = $this->selectCountQuery($countQuery, $this->listBindValues);
        if($count !== false)
        {
            $this->count = $count;
            return $this->selectQueryObjets($this->listQuery, $this->listBindValues);
        }
        return false;
    }

    /**
     * @param string|null $forignKey
     * @param mixed|null $forign_key_value
     * @return array<mixed>|false
     */
    public function ListDeactivesQuery(string $forignKey = null , mixed $forign_key_value = null): array|false
    {
        $whereForignKey = '';
        if($forignKey)
        {
            $whereForignKey = " AND {$forignKey} = :key_id";
            $this->listBindValues[':key_id'] = $forign_key_value;
        }
        $this->listQuery = "SELECT * FROM {$this->setTable()} WHERE is_active = 0 {$whereForignKey} {$this->where} {$this->find} {$this->between} {$this->orderBy} {$this->page}";
        $countQuery = "SELECT COUNT(*) FROM {$this->setTable()} WHERE is_active = 0 {$whereForignKey} {$this->where} {$this->find} {$this->between}";
        $count = $this->selectCountQuery($countQuery, $this->listBindValues);
        if($count !== false)
        {
            $this->count = $count;
            return $this->selectQueryObjets($this->listQuery, $this->listBindValues);
        }
        return false;
    }

    /**
     * @param string|null $forignKey
     * @param mixed|null $forign_key_value
     * @return array<mixed>|false
     */
    public function ListActivesQuery(string $forignKey = null , mixed $forign_key_value = null): array|false
    {
        $whereForignKey = '';
        if($forignKey)
        {
            $whereForignKey = " AND {$forignKey} = :key_id";
            $this->listBindValues[':key_id'] = $forign_key_value;
        }
        $this->listQuery = "SELECT * FROM {$this->setTable()} WHERE is_active = 1 {$whereForignKey} {$this->find} {$this->between} {$this->orderBy} {$this->page}";
        $countQuery = "SELECT COUNT(*) FROM {$this->setTable()} WHERE is_active = 1 {$whereForignKey} {$this->find} {$this->between}";
        $count = $this->selectCountQuery($countQuery, $this->listBindValues);
        if($count !== false)
        {
            $this->count = $count;
            return $this->selectQueryObjets($this->listQuery, $this->listBindValues);
        }
        return false;
    }

    /**
     * @param string|null $forignKey
     * @param mixed|null $forign_key_value
     * @return array<mixed>|false
     */
    public function ListTrashQuery(string $forignKey = null , mixed $forign_key_value = null): array|false
    {
        $whereForignKey = '';
        if($forignKey)
        {
            $whereForignKey = " AND {$forignKey} = :key_id";
            $this->listBindValues[':key_id'] = $forign_key_value;
        }
        $this->listQuery = "SELECT * FROM {$this->setTable()} WHERE deleted_at IS NOT NULL {$whereForignKey} {$this->where} {$this->where} {$this->find} {$this->between} {$this->orderBy} {$this->page}";
        
        $countQuery = "SELECT COUNT(*) FROM {$this->setTable()} WHERE deleted_at IS NOT NULL  {$whereForignKey} {$this->where} {$this->where} {$this->find} {$this->between}";
        $count = $this->selectCountQuery($countQuery, $this->listBindValues);
        if($count !== false)
        {
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
        $found = $this->selectQueryObjets("SELECT * FROM {$this->setTable()} WHERE id = :id LIMIT 1",[':id'=>$id]);
        if($found === false)
        {
            return false;
        }
        if(count($found) === 0)
        {
            return null;
        }
        /** @phpstan-ignore-next-line */
        return $found[0];
    }
}
