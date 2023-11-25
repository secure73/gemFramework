<?php

namespace GemFramework\Traits\Table;

/**
 * @method activateQuery()
 * @method deactivateQuery()
 * Activate and Deactivate
 * it need column isActive column in Database also is_active Properties
 */
trait ActivateQueryTrait
{
    /**
     * this trait deliver tow methods:
     * activate():int|null set isActive to 1
     * deactivate():int|null set isActive to 0
     * Important : need column isActive boolean 
     * @return int|null
     * @Update current instance isActive = 1
     *
     * @will return lastInsertedId
     *
     * @you can call affectedRows() and it shall be 1
     *
     * @error: $this->getError();
     */
    public function activateQuery():int|null
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
        if(!property_exists($this,'is_active'))
        {
            $this->setError('property is_active does existed or not setted in object');
            return null;
        }
        $query = "UPDATE $table SET is_active = 1 WHERE id = :id";
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
    public function deactivateQuery():int|null
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
        if(!property_exists($this,'is_active'))
        {
            $this->setError('property is_active does existed or not setted in object');
            return null;
        }
        $query = "UPDATE $table SET is_active = 0 WHERE id = :id";
        $arrayBind[':id'] = $this->id;
        return $this->updateQuery($query, $arrayBind);
    }

    public function selectActivesQuery()
    {
        $table = $this->setTable();
        if(!$table)
        {
            $this->setError('table is not setted in function setTable');
            return null;
        }
        return $this->selectByColumns('is_active',\SqlEnumCondition::Equal,1);
    }
}
