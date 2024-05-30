<?php
namespace Gemvc\Traits\Table;

/**
 * @method insertSingleQuery()
 * insert single row to table
 */
trait InsertSingleQueryTrait
{
    /**
     * @return int|false Last inserted ID or false on failure
     * @throws \Exception If failure also throws an exception
     * insert single row to table
     */
    public final function insertSingleQuery(): int|false
    {
        $table = $this->getTable();
        if (!$table) {
            $this->setError('Table is not set in function getTable');
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
