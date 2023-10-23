<?php
namespace GemFramework\Traits\Table;

/**
 * @method id()
 * @method ids()
 * @method firstRows()
 * @method lastRows()
 * @method columnSelect()
 * select object with given id or array of objects by giving ids
 */
trait SelectTrait{

    /**
     * @param int $id
     * @return bool
     * set $this value and return true if found, false otherwise
     */
    public function id(int $id): bool
    {
        $table = $this->setTable();
        if(!$table)
        {
            $this->setError('table is not setted in function setTable');
            return false;
        }
        $row = $this->selectQuery("SELECT * FROM $table WHERE id = :id", [':id' => $id]);
        if (null !== $row && isset($row[0]) && \is_array($row[0])) {
            $this->fetchObject($row[0]);
            return true;
        } else {
            $this->setError('Object with given id dose not Existed');
        }
        return false;
    }

    
    /**
     * @param array<int> $ids
     * @return null|array<$this>
     * in case of failure return null
     */
    public function ids(array $ids): array|null
    {
        $table = $this->setTable();
        if(!$table)
        {
            $this->setError('table is not setted in function setTable');
            return false;
        }
        $stringIds = '';
        foreach ($ids as $id) {
            $stringIds .= $id.',';
        }
        $stringIds = rtrim($stringIds, ',');
        $query = "SELECT * FROM {$table} WHERE id IN ({$stringIds})";
        $queryResult = $this->selectQuery($query, []);
        if(is_array($queryResult))
        {
            return $this->fetchAllObjects($queryResult);
        }
        return null;
    }


    /**
     * @return null|array<$this>
     */
    public function firstRows(
        int $countRows,
        string $whereColumn,
        \SqlEnumCondition $whereCondition,
        mixed $whereValue,
        ?string $orderByColumnName = null
    ): null|array {
        $table = $this->setTable();
        if(!$table)
        {
            $this->setError('table is not setted in function setTable');
            return false;
        }
        $arrayBindValue = [];
        $where = " WHERE {$whereColumn} ".$whereCondition->value." :{$whereColumn}";
        $arrayBindValue[':'.$whereColumn] = $whereValue;
        $query = ($orderByColumnName) ? 
                "SELECT * FROM {$table} ORDER BY {$orderByColumnName} {$where} LIMIT {$countRows}" :
                "SELECT * FROM {$table} {$where} LIMIT {$countRows}";
        $queryResult = $this->selectQuery($query,  $arrayBindValue);
        if(is_array($queryResult))
        {
            return $this->fetchAllObjects($queryResult);
        }
        return null;
    }


    /**
     * @return null|array<$this>
     */
    public function lastRows(int $countRows, string $orderByColumnName, ?string $whereColumn = null, ?\SqlEnumCondition $whereCondition = null, int|string|bool $whereValue = null): null|array
    {
        $table = $this->setTable();
        if(!$table)
        {
            $this->setError('table is not setted in function setTable');
            return false;
        }
        $arrayBindValue = [];
        $where = '';
        if (null !== $whereColumn && null !== $whereCondition) {
            $where = " WHERE {$whereColumn} ".$whereCondition->value;
        }
        if (null !== $whereValue) {
            $where .= " :{$whereValue}";
            $arrayBindValue[':'.$whereValue] = $whereValue;
        }

        $query = "SELECT * FROM {$table} ORDER BY {$orderByColumnName} DESC {$where} LIMIT {$countRows}";

        $queryResult = $this->selectQuery($query, $arrayBindValue);
        if(is_array($queryResult))
        {
            return $this->fetchAllObjects($queryResult);
        }
        return null;
    }


     /**
     * @return null|array<$this>
     */
    public function columnSelect(
        ?string $firstColumn = null,
        ?\SqlEnumCondition $firstCondition = null,
        mixed $firstValue = null,
        ?string $secondColumn = null,
        ?\SqlEnumCondition $secondCondition = null,
        mixed $secondValue = null,
        ?string $orderBy = null,
        ?string $ASC_DES = null,
        ?int $limit_count = null,
        ?int $limit_offset = null,
        ?bool $isDel = null,
        ?bool $deactives = null,
        ?bool $actives = null

    ): null|array {
        $table = $this->setTable();
        if(!$table)
        {
            $this->setError('table is not setted in function setTable');
            return false;
        }
        $limit = '';
        $arrayBindValue = [];

        $isDel ? ' AND deleted_at IS NOT NULL ' : '';
        $actives ? ' AND isActive = 1 ' : '';
        
        $deactives ? ' AND isActive IS NOT 1 ' : '';
        if ($orderBy) {
            $orderBy = " ORDER BY {$orderBy} {$ASC_DES}";
        }
        if ($limit_count) {
            $limit = " LIMIT {$limit_count}";
            if ($limit_offset) {
                $limit = " LIMIT {$limit_offset} , {$limit_count}";
            }
        }

        $query = "SELECT * FROM {$table} ";
        $firstColumnQuery = null;
        $secondColumnQuery = null;

        if(null !== $firstColumn && null !== $firstCondition)
        {
            $firstValue = (' LIKE ' === (string) $firstCondition->value) ? '%'.$firstValue : $firstValue;
            $firstColumnQuery = " {$firstColumn} {$firstCondition->value} :{$firstColumn}";
            $arrayBindValue[':'.$firstColumn] = $firstValue;
        }

        if (null !== $secondColumn && null !== $secondCondition) {
            $secondColumnQuery = " AND {$secondColumn} {$secondCondition->value} :{$secondColumn}";
            $secondValue = (' LIKE ' === $secondCondition->value) ? '%'.$secondValue : $secondValue;
            $arrayBindValue[':'.$secondColumn] = $secondValue;
        }
        $query .= "WHERE  {$firstColumnQuery} {$secondColumnQuery} {$isDel} {$actives} {$deactives} {$orderBy} {$limit}";
        $query = trim($query);
        //echo $query;
        $queryResult = $this->selectQuery($query, $arrayBindValue);
        if(is_array($queryResult))
        {
            return $this->fetchAllObjects($queryResult);
        }
        return null;
    }

}