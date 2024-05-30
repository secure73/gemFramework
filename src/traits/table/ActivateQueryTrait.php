<?php

namespace Gemvc\Traits\Table;

/**
 * @method activateQuery(?int $id)
 * it need column is_active in Database also  
 * public int $is_active in extended class of Table 
 */
trait ActivateQueryTrait
{
    public function activateQuery(?int $id = null):int|null
    {
        $table = $this->getTable();
        if(!$table)
        {
            $this->setError('table is not setted in function getTable');
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
}
