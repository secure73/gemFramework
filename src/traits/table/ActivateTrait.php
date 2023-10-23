<?php

namespace GemFramework\Traits\Table;

/**
 * @method activate()
 * @method deactivate()
 * Activate and Deactivate
 * it need column isActive column in Database also isActive Properties
 */
trait ActivateTrait
{
    /**
     * @return int|null
     * @Update current instance isActive = 1
     *
     * @will return lastInsertedId
     *
     * @you can call affectedRows() and it shall be 1
     *
     * @error: $this->getError();
     */
    public function activate():int|null
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
        if(!property_exists($this,'isActive'))
        {
            $this->setError('property isActive does existed or not setted in object');
            return null;
        }
        $query = "UPDATE $table SET isActive = 1 WHERE id = :id";
        $arrayBind[':id'] = $this->id;
        return $this->updateQuery($query, $arrayBind);
    }

    /**
     * @return int|null
     * @Update current instance isActive = 0
     *
     * @will return 1 in success and null in failure
     *
     * @you can call affectedRows() and it shall be 1
     *
     * @error: $this->getError();
     */
    public function deactivate():int|null
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
        if(!property_exists($this,'isActive'))
        {
            $this->setError('property isActive does existed or not setted in object');
            return null;
        }
        $query = "UPDATE $table SET isActive = 0 WHERE id = :id";
        $arrayBind[':id'] = $this->id;
        return $this->updateQuery($query, $arrayBind);
    }
}
