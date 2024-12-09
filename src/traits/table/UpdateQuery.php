<?php
namespace Gemvc\Traits\Table;
use Gemvc\Http\Response;

/**
 * @method updateSingleQuery()
 * @method setNullQuery()
 * @method setTimeNowQuery()
 */
trait UpdateQuery
{
    /**
     * @return object<$this>
     * @error: $this->getError();
     */
    public final function updateSingleQuery(): object
    {
        $result = false;
        $table = $this->getTable();
        if (!$table) {
            $this->setError('table is not set in function getTable');
            Response::internalError($this->getError())->show();
            die();

        }
        if (!isset($this->id) || $this->id < 1) {
            $this->setError("property id does existed or not set in object for update Query $this->getTable()");
            Response::internalError($this->getError())->show();
            die();
        }
        $arrayBind = [];
        $table = $this->getTable();
        if ($this->id > 0) {
            $query = "UPDATE $table SET ";
            // @phpstan-ignore-next-line
            foreach ($this as $key => $value) {
                $query .= " {$key} = :{$key},";
                $arrayBind[":{$key}"] = $value;
            }
            $query = rtrim($query, ',');
            $query .= ' WHERE id = :id';
            $arrayBind[':id'] = $this->id;
            $result = $this->updateQuery($query, $arrayBind);
        }
        if($result === false) {
            Response::internalError("error in update Query: $this->getTable(): ".$this->getError())->show();
            die();
        }
        return $this;
    }


    /**
     * @return int affected rows or Show Internal Error and die
     * @set a specific column to null based on condition whereColumn = $whereValue
     *
     * @exampel $this->setNull('deleted_at,'id',$this->id);
     *
     * @explain:  set deleted_at to null where id = $this->id
     */
    public function setNullQuery(string $columnNameSetToNull, string $whereColumn, mixed $whereValue): int
    {
        $table = $this->getTable();
        if (!$table) {
            $this->setError('table is not set in function getTable');
            Response::internalError($this->getError())->show();
            die();

        }
        $query = "UPDATE {$table}  SET  {$columnNameSetToNull} = NULL  WHERE  {$whereColumn}  = :whereValue";

        $result = $this->updateQuery($query, [':whereValue' => $whereValue]);
        if($this->getError() || $result === false) {
            Response::internalError("error in update Query: $this->getTable(): ".$this->getError())->show();
            die();
        }
        return $result;
    }

    /**
     * @return int affected rows or Show Internal Error and die
     * @set a specific column to time now based on condition whereColumn = $whereValue
     *
     * @exampel $order->setTimeNow('paid_at','id',$this->id);
     *
     * @explain:  set paid_at  to 18-08-2022 12:45:13 where id = $this->id
     */
    public function setTimeNowQuery(string $columnNameSetToNowTomeStamp, string $whereColumn, mixed $whereValue): int
    {
        $table = $this->getTable();
        if (!$table) {
            $this->setError('table is not set in function getTable');
            Response::internalError($this->getError())->show();
            die();
        }


        $query = "UPDATE {$table}  SET  {$columnNameSetToNowTomeStamp} = NOW()  WHERE  {$whereColumn}  = :whereValue";
        $result = $this->updateQuery($query, [':whereValue' => $whereValue]);
        if($this->getError() || $result === false) {
            Response::internalError("error in update Query: $this->getTable(): ".$this->getError())->show();
            die();
        }
        return $result;
    }
}
