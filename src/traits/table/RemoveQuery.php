<?php
namespace Gemvc\Traits\Table;
use Gemvc\Http\Response;

/**
 * @method removeQuery()
 * @method removeConditionalQuery()
 * NOTE !! This trait remove row from database
 */
trait RemoveQuery {
    /**
     * @param int $id
     * @return int
     * NOTE:  remove Object from Database.
     * @ in case of success return count removed items.
     */
    public final function removeQuery(int $id): int
    {
        return $this->removeConditionalQuery('id', $id);
    }

    /**
     * NOTE:  remove Object completely from Database.
     * @ in case of success return count removed items
     * @Attention:  remove Object completely from Database
     */
    public function removeConditionalQuery(string $whereColumn, mixed $whereValue, ?string $secondWhereColumn = null, mixed $secondWhereValue = null): int
    {
        $table = $this->getTable();
        if (!$table) {
            Response::internalError('Table is not set in function getTable.')->show();
           die();
        }

        $query = "DELETE FROM {$table} WHERE {$whereColumn} = :{$whereColumn}";
        if ($secondWhereColumn) {
            $query .= " AND {$secondWhereColumn} = :{$secondWhereColumn}";
        }

        $arrayBind = [':'.$whereColumn => $whereValue];
        if ($secondWhereColumn) {
            $arrayBind[':'.$secondWhereColumn] = $secondWhereValue;
        }
        $result = $this->deleteQuery($query, $arrayBind);
        if($result === false) {
            Response::internalError("error in delete Query:". $this->getTable() .",".$this->getError())->show();
            die();
        }
        return $result;
    }
}
