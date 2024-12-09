<?php
namespace Gemvc\Traits\Table;
use Gemv\Core\Table;
use Gemvc\Http\Response;
/**
 * @method insertSingleQuery()
 * insert single row to table
 */
trait InsertSingleQuery
{
    /**
     * @return object<$this>
     * @error: $this->getError();
     */
    public final function insertSingle(): object
    {
        $table = $this->getTable();
        if (!$table) {
            Response::internalError($this->getError())->show();
            die();
        }

        $columns = '';
        $params = '';
        $arrayBind = [];
        $query = "INSERT INTO {$table} ";

        foreach ($this as $key => $value) {
            $columns .= $key . ',';
            $params .= ':' . $key . ',';
            $arrayBind[':' . $key] = $value;
        }

        $columns = rtrim($columns, ',');
        $params = rtrim($params, ',');

        $query .= " ({$columns}) VALUES ({$params})";
        $result = $this->insertQuery($query, $arrayBind);
        if( $this->getError() || $result === false) {
            Response::internalError("error in insert Query: $this->getTable(): ".$this->getError())->show();
            die();
        }
        $this->id = $result;
        return $this;
    }

}
