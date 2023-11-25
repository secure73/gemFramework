<?php

namespace GemFramework\Traits\Table;

/**
 * This trait will insert object into database
 * @method insert()
 */
trait InsertQueryTrait
{
    /**
     * Insert current instance into Database
     *
     * @return int|false Last inserted ID or false on failure
     *
     * @throws \Exception If insertQuery method throws an exception
     */
    public final function insertSingleQuery(): int|false
    {
        $table = $this->setTable();
        if (!$table) {
            $this->setError('Table is not set in function setTable');
            return false;
        }

        $columns = '';
        $params = '';
        $arrayBind = [];
        $query = "INSERT INTO {$table} ";

        foreach ((object) $this as $key => $value) {
            $columns .= $key . ',';
            $params .= ':' . $key . ',';
            $arrayBind[':' . $key] = $value;
        }

        $columns = rtrim($columns, ',');
        $params = rtrim($params, ',');

        $query .= " ({$columns}) VALUES ({$params})";
        return $this->insertQuery($query, $arrayBind);
    }

}
