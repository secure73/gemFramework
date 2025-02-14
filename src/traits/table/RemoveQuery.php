<?php
namespace Gemvc\Traits\Table;
use Gemvc\Http\JsonResponse;
use Gemvc\Http\Response;

/**
 * @method JsonResponse remove():JsonResponse
 * @method removeQuery(int $id):int
 * @method removeConditionalQuery(string $whereColumn, mixed $whereValue, ?string $secondWhereColumn = null, mixed $secondWhereValue = null):int
 * NOTE !! This trait remove row from database
 */
trait RemoveQuery
{
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

    public function remove(): JsonResponse
    {
        if(!isset($this->id))
        {
            return Response::badRequest("id for this object is not set");
        }
        if(!$this->id || $this->id < 1){
            return Response::badRequest("id shall be positive int");
        }
        $this->removeConditionalQuery("id", $this->id);
        return Response::deleted($this, 1, "removed successfully");
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

        $arrayBind = [':' . $whereColumn => $whereValue];
        if ($secondWhereColumn) {
            $arrayBind[':' . $secondWhereColumn] = $secondWhereValue;
        }
        $result = $this->deleteQuery($query, $arrayBind);
        if ($result === false) {
            Response::internalError("error in delete Query:" . $this->getTable() . "," . $this->getError())->show();
            die();
        }
        return $result;
    }
}
