<?php
namespace GemFramework\Traits\Table;
<<<<<<< HEAD
=======

/**
 * @method remove
 * @method RemoveConditional
 */
>>>>>>> e1c71f343beb225d113822736b4f7f3d04a71b07
trait RemoveTrait
{
    /**
     * @ in case of success return 1
     * @Attention:  remove Object compleetly from Database and only work with property id
     */
    public function remove(): int|null
    {
        $table = $this->setTable();
        if(!$table)
        {
            $this->setError('table is not setted in function setTable');
            return null;
        }
        if(!isset($this->id))
        {
            $this->setError('property id does existed or not setted in object');
            return null;
        }
        $query = "DELETE FROM {$table} WHERE id = :id";
        if (isset($this->id) && $this->id > 0) {
            return $this->deleteQuery($query, [':id' => $this->id]);
        }
        $this->setError('Object id is not set or it is less than 1');

        return null;
    }

    /**
     * NOTE:  remove Object compleetly from Database.
     * @ in case of success return count removed items
     * @Attention:  remove Object compleetly from Database
     */
    public function RemoveConditional(string $whereColumn, mixed $whereValue, ?string $secondWhereColumn = null, mixed $secondWhereValue = null): int|null
    {
        $table = $this->setTable();
        if(!$table)
        {
            $this->setError('table is not setted in function setTable');
            return null;
        }
        $query = "DELETE FROM {$table} WHERE {$whereColumn} = :{$whereColumn}";
        if ($secondWhereColumn) {
            $query .= " AND {$secondWhereColumn} = :{$secondWhereColumn}";
        }
        $arrayBind[':'.$whereColumn] = $whereValue;
        if ($secondWhereColumn) {
            $arrayBind[':'.$secondWhereColumn] = $secondWhereValue;
        }
        return $this->deleteQuery($query, $arrayBind);
    }
}