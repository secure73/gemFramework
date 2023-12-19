<?php

namespace GemFramework\Traits\Table;

/**
 * @method activateQuery()
 * @method deactivateQuery()
 * it need column is_active in Database also  public int $is_active in extended class of Table 
 */
trait ActivateQueryTrait
{
    /**
     * @param ?int $id
     * @return int|null
     * @Update is_active to 1
     * @you can call affectedRows() and it shall be 1
     * @error: $this->getError();
     */
    public function activateQuery(?int $id = null):int|null
    {
        $table = $this->setTable();
        if(!$table)
        {
            $this->setError('table is not setted in function setTable');
            return null;
        }
        if($id)
        {
            $this->id = $id;
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
        return $this->updateQuery("UPDATE $table SET is_active = 1 WHERE id = :id", [':id' => $this->id]);
    }

    /**
     * @param ?int $id
     * @return int|null
     * @Update is_active to 0
     * @you can call affectedRows() and it shall be 1
     * @error: $this->getError();
     */
    public function deactivateQuery(?int $id = null):int|null
    {
        $table = $this->setTable();
        if(!$table)
        {
            $this->setError('ActivateQueryTrait: table is not setted in function setTable');
            return null;
        }
        if($id)
        {
            $this->id = $id;
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
        return $this->updateQuery("UPDATE $table SET is_active = 0 WHERE id = :id", [':id' => $this->id]);
    }
}
