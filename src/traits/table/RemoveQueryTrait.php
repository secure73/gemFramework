<?php
namespace GemFramework\Traits\Table;
/**
 * NOTE !! This trait will remove Permanently object into database
 * @method removeQuery()
 * @method removeConditionalQuery()
 */
trait RemoveQueryTrait {
    /**
     * NOTE:  remove Object from Database.
     * @ in case of success return count removed items
     * @Attention:  remove Object from Database
     * @return int|null
     */
    public function removeQuery(): int|null
    {
        if(!isset($this->id))
        {
            $this->setError('property id does not exist or is not set in object');
            return null;
        }

        return $this->removeConditionalQuery('id', $this->id);
    }

    /**
     * NOTE:  remove Object compleetly from Database.
     * @ in case of success return count removed items
     * @Attention:  remove Object compleetly from Database
     */
    public function removeConditionalQuery(string $whereColumn, mixed $whereValue, ?string $secondWhereColumn = null, mixed $secondWhereValue = null): int|null
    {
        $table = $this->setTable();
        if (!$table) {
            $this->setError('Table is not set in function setTable.');
            return null;
        }

        $query = "DELETE FROM {$table} WHERE {$whereColumn} = :{$whereColumn}";
        if ($secondWhereColumn) {
            $query .= " AND {$secondWhereColumn} = :{$secondWhereColumn}";
        }

        $arrayBind = [':'.$whereColumn => $whereValue];
        if ($secondWhereColumn) {
            $arrayBind[':'.$secondWhereColumn] = $secondWhereValue;
        }
        return $this->deleteQuery($query, $arrayBind);
    }
}
