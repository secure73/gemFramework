<?php
namespace GemFramework\Traits\Table;

/**
 * @method updateSingleQuery()
 * @method setNullQuery()
 * @method setTimeNowQuery()
 */
trait UpdateQueryTrait
{
    /**
     * @update current instance
     * @you can call affectedRows() and it shall be 1
     * @error: $this->getError();
     */
    public function updateSingleQuery(): int|null
    {
        $table = $this->setTable();
        if (!$table) {
            $this->setError('table is not set in function setTable');
            return null;
        }
        if (!isset($this->id) || $this->id < 1) {
            $this->setError('property id does existed or not set in object');
            return null;
        }
        $arrayBind = [];
        $table = $this->setTable();
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
            return $this->updateQuery($query, $arrayBind);
        }
        return null;
    }


    /**
     * @set a specific column to null based on condition whereColumn = $whereValue
     *
     * @exampel $this->setNull('deleted_at,'id',$this->id);
     *
     * @explain:  set deleted_at to null where id = $this->id
     */
    public function setNullQuery(string $columnNameSetToNull, string $whereColumn, mixed $whereValue): int|null
    {
        $table = $this->setTable();
        if (!$table) {
            $this->setError('table is not set in function setTable');
            return null;
        }
        $query = "UPDATE {$table}  SET  {$columnNameSetToNull} = NULL  WHERE  {$whereColumn}  = :whereValue";

        return $this->updateQuery($query, [':whereValue' => $whereValue]);
    }

    /**
     * @set a specific column to time now based on condition whereColumn = $whereValue
     *
     * @exampel $order->setTimeNow('paid_at','id',$this->id);
     *
     * @explain:  set paid_at  to 18-08-2022 12:45:13 where id = $this->id
     */
    public function setTimeNowQuery(string $columnNameSetToNowTomeStamp, string $whereColumn, mixed $whereValue): int|null
    {
        $table = $this->setTable();
        if (!$table) {
            $this->setError('table is not set in function setTable');
            return null;
        }
        $query = "UPDATE {$table}  SET  {$columnNameSetToNowTomeStamp} = NOW()  WHERE  {$whereColumn}  = :whereValue";

        return $this->updateQuery($query, [':whereValue' => $whereValue]);
    }
}
