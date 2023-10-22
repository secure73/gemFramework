<?php

namespace GemFramework\Traits\Table;

/**
 * insert New Object into Database
 */
trait InsertTrait
{
    /**
     * @insert current instance into Database
     *
     * @will return lastInsertedId
     *
     * @you can call affectedRows() and it shall be 1
     *
     * @error: $this->getError();
     */
    public function update(): int|null
    {
        $table = $this->setTable();
        if(!$table)
        {
            $this->setError('table is not setted in function setTable');
            return null;
        }
        if(!isset($this->id) || $this->id < 1)
        {
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
}
