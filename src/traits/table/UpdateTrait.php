<?php

namespace GemFramework\Traits\Table;

/**
 *this trait update single column with property id 
 *method : update():int|null;
 * @update current instance
 * @you can call affectedRows() and it shall be 1
 * @error: $this->getError();
 */
trait UpdateTrait
{
    /**
     * @update current instance
     * @you can call affectedRows() and it shall be 1
     * @error: $this->getError();
     */
    public function update(): int|null
    {
        $table = $this->setTable();
        if (!$table) {
            $this->setError('table is not setted in function setTable');
            return null;
        }
        if (!isset($this->id) || $this->id < 1) {
            $this->setError('property id does existed or not setted in object');
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
    public function setNull(string $columnNameSetToNull, string $whereColumn, mixed $whereValue): int|null
    {
        $table = $this->setTable();
        if (!$table) {
            $this->setError('table is not setted in function setTable');
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
    public function setTimeNow(string $columnNameSetToNowTomeStamp, string $whereColumn, mixed $whereValue): int|null
    {
        $table = $this->setTable();
        if (!$table) {
            $this->setError('table is not setted in function setTable');
            return null;
        }
        $query = "UPDATE {$table}  SET  {$columnNameSetToNowTomeStamp} = NOW()  WHERE  {$whereColumn}  = :whereValue";

        return $this->updateQuery($query, [':whereValue' => $whereValue]);
    }
}
