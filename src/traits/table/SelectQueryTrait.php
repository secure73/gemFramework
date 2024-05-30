<?php

namespace Gemvc\Traits\Table;

/**
 * @method selectByIdQuery()
 * @method selectByIdsQuery()
 * @method selectFirstRowsQuery()
 * @method selectLastRowsQuery()
 * @method selectByColumnsQuery()
 * select object with given id or array of objects by giving ids
 */
trait SelectQueryTrait
{
    /**
     * @param int|null $id
     * @return bool
     * Set $this value and return true if found, false otherwise
     */
    public function selectByIdQuery(?int $id = null): bool
    {
        $table = $this->getTable();
        if (!$table) {
            $this->setError('Table is not set in function getTable');
            return false;
        }
        if ($id) {
            $this->id = $id;
        }
        if (!isset($this->id) || $this->id < 1) {
            $this->setError('Property id does not exist or is not set in object');
            return false;
        }

        $select_result =  $this->selectQuery("SELECT * FROM {$table} WHERE id = :id LIMIT 1", [':id' => $id]);
        if($select_result == false)
        {
            $this->setError('Failed to select row from table:'.$this->getError());
            return false;
        }
        if(count($select_result) == 0)
        {
            $this->setError('No row found with id:'.$id);
            return false;
        }
        $select_result = $select_result[0];
        foreach ($select_result as $key => $value) {
            $this->$key = $value;
        }
        return true;
    }

    /**
     * @param array<int> $ids
     * @return null|array<$this>
     * in case of failure return null
     */
    public function selectByIdsQuery(array $ids): ?array
    {
        $table = $this->getTable();
        if (!$table) {
            $this->setError('table is not set in function getTable');
            return null;
        }
        if (count($ids) == 0) {
            $this->setError('ids array is empty');
            return null;
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $query = "SELECT * FROM {$table} WHERE id IN ({$placeholders})";

        $result = $this->selectQueryObjets($query, []);
        if ($result === false) {
            $this->setError('Failed to select rows from table:'. $this->getError());
            return null;
        }
        if(count($result) < 1)
        {
            $this->setError('No rows found within ids:'.implode(',',$ids));
            return null;
        }
        return $result;
    }



    /**
     * Select the first rows from the table based on the given conditions.
     *
     * @param int $countRows The number of rows to select.
     * @param string $whereColumn The name of the column to search by.
     * @param \SqlEnumCondition $whereCondition The condition to apply to the column.
     * @param mixed $whereValue The value to search for in the column.
     * @param string|null $orderByColumnName The name of the column to order the results by.
     * @return array|null An array of objects representing the selected rows, or null if the selection failed.
     */
    public function selectFirstRowsQuery(
        int $countRows,
        string $whereColumn,
        \SqlEnumCondition $whereCondition,
        mixed $whereValue,
        ?string $orderByColumnName = null
    ): ?array {
        $table = $this->getTable();
        if (!$table) {
            $this->setError('Table is not set in function getTable');
            return null;
        }

        $whereClause = "WHERE {$whereColumn} {$whereCondition->value} :{$whereColumn}";
        $orderByClause = $orderByColumnName ? "ORDER BY {$orderByColumnName}" : "";
        $limitClause = "LIMIT {$countRows}";

        $query = "SELECT * FROM {$table} {$whereClause} {$orderByClause} {$limitClause}";
        $statement = $this->prepareQuery($query);
        $statement->bindValue(":{$whereColumn}", $whereValue);

        $queryResult = $this->executeQuery($statement);
        if ($queryResult === false) {
            $this->setError('Failed to select rows from table');
            return null;
        }

        return $this->fetchAllObjects($queryResult);
    }


    /**
     * Select the last rows from the table based on the given conditions.
     *
     * @param int $countRows The number of rows to select.
     * @param string $orderByColumnName The name of the column to order the results by.
     * @param string|null $whereColumn The name of the column to search by.
     * @param \SqlEnumCondition|null $whereCondition The condition to apply to the column.
     * @param int|string|bool|null $whereValue The value to search for in the column.
     * @return array|null An array of objects representing the selected rows, or null if the selection failed.
     */
    public function selectLastRowsQuery(
        int $countRows,
        string $orderByColumnName,
        ?string $whereColumn = null,
        ?\SqlEnumCondition $whereCondition = null,
        int|string|bool|null $whereValue = null
    ): ?array {
        $table = $this->getTable();
        if (!$table) {
            $this->setError('table is not set in function getTable');
            return null;
        }

        $whereClause = '';
        $bindValues = [];

        // Add WHERE clause if necessary
        if ($whereColumn !== null && $whereCondition !== null && $whereValue !== null) {
            $whereClause = " WHERE {$whereColumn} {$whereCondition->value} :whereValue";
            $bindValues = [':whereValue' => $whereValue];
        }

        $query = "SELECT * FROM {$table} {$whereClause} ORDER BY {$orderByColumnName} DESC LIMIT {$countRows}";

        $queryResult = $this->selectQuery($query, $bindValues);
        if (is_array($queryResult)) {
            return $this->fetchAllObjects($queryResult);
        }
        return null;
    }


    /**
     * Select rows from the table based on the given columns and conditions.
     *
     * @param string $firstColumn The name of the first column to search by.
     * @param \SqlEnumCondition $firstCondition The condition to apply to the first column.
     * @param mixed $firstValue The value to search for in the first column.
     * @param string|null $secondColumn The name of the second column to search by.
     * @param \SqlEnumCondition|null $secondCondition The condition to apply to the second column.
     * @param mixed|null $secondValue The value to search for in the second column.
     * @param string|null $orderBy The name of the column to order the results by.
     * @param string|null $ASC_DES The order in which to sort the results (ASC or DESC).
     * @param int|null $limit_count The maximum number of rows to return.
     * @param int|null $limit_offset The number of rows to skip before starting to return results.
     * @param bool|null $isDel Whether to include deleted rows in the results.
     * @param bool|null $deactivates Whether to include inactive rows in the results.
     * @param bool|null $actives Whether to include active rows in the results.
     * @return null|array<$this> An array of objects representing the selected rows, or null if the selection failed.
     */
    public function selectByColumnsQuery(
        string $firstColumn,
        \SqlEnumCondition $firstCondition,
        mixed $firstValue,
        ?string $secondColumn = null,
        ?\SqlEnumCondition $secondCondition = null,
        mixed $secondValue = null,
        ?string $orderBy = null,
        ?string $ASC_DES = null,
        ?int $limit_count = null,
        ?int $limit_offset = null,
        ?bool $isDel = null,
        ?bool $deactivates = null,
        ?bool $actives = null
    ): null|array {
        $table = $this->getTable();
        if (!$table) {
            $this->setError('table is not set in function getTable');
            return null;
        }

        $where = '';
        $arrayBindValue = [];

        // Add conditions to the WHERE clause based on the input parameters.
        $where .= " WHERE {$firstColumn} " . $firstCondition->value . " :{$firstColumn}";
        $arrayBindValue[':' . $firstColumn] = $firstValue;

        if ($secondColumn && $secondCondition && $secondValue) {
            $where .= " AND {$secondColumn} " . $secondCondition->value . " :{$secondColumn}";
            $arrayBindValue[':' . $secondColumn] = $secondValue;
        }

        if ($isDel) {
            $where .= ' AND deleted_at IS NOT NULL';
        }

        if ($actives) {
            $where .= ' AND is_active = 1';
        }

        if ($deactivates) {
            $where .= ' AND is_active = 0';
        }

        // Add an ORDER BY clause if specified.
        $orderByClause = '';
        if ($orderBy) {
            $orderByClause = " ORDER BY {$orderBy}";
            if ($ASC_DES) {
                $orderByClause .= " {$ASC_DES}";
            }
        }

        // Add a LIMIT clause if specified.
        $limitClause = '';
        if ($limit_count) {
            $limitClause = " LIMIT {$limit_count}";
            if ($limit_offset) {
                $limitClause .= " OFFSET {$limit_offset}";
            }
        }

        // Construct the final query and execute it.
        $query = "SELECT * FROM {$table}{$where}{$orderByClause}{$limitClause}";
        $queryResult = $this->selectQuery($query, $arrayBindValue);

        // Return the results as an array of objects, or null if the selection failed.
        if (is_array($queryResult)) {
            return $this->fetchAllObjects($queryResult);
        }
        return null;
    }
}
