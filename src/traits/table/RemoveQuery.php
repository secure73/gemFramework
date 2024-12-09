<?php
namespace Gemvc\Traits\Table;
/**
 * @method removeQuery()
 * @method removeConditionalQuery()
 * NOTE !! This trait remove row from database
 */
trait RemoveQuery {
    /**
     * @param int|null $id
     * @return int|null
     * NOTE:  remove Object from Database.
     * @ in case of success return count removed items
     */
    public final function removeQuery(int $id): int|null
    {
        $this->id = $id;

        if(!isset($this->id))
        {
            $this->setError('property id does not exist or is not set in object');
            return null;
        }

        return $this->removeConditionalQuery('id', $this->id);
    }

    /**
     * NOTE:  remove Object completely from Database.
     * @ in case of success return count removed items
     * @Attention:  remove Object completely from Database
     */
    public function removeConditionalQuery(string $whereColumn, mixed $whereValue, ?string $secondWhereColumn = null, mixed $secondWhereValue = null): int|null
    {
        $table = $this->getTable();
        if (!$table) {
            $this->setError('Table is not set in function getTable.');
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
