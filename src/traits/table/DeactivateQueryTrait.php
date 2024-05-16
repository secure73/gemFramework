<?php

namespace GemFramework\Traits\Table;

/**
 * @method deactivateQuery()
 * it need column is_active in Database 
 * also public int $is_active in extended class of Table 
 */
trait DeactivateQueryTrait
{
    /**
     * @param ?int $id
     * @return int|null
     * @Update is_active to 0
     * @you can call affectedRows() and it shall be 1
     * @error: $this->getError();
     */
    public function deactivateQuery(?int $id = null):int|null
    {
        $table = $this->getTable();
        if(!$table)
        {
            $this->setError('ActivateQueryTrait: table is not set in function getTable');
            return null;
        }
        if($id)
        {
            $this->id = $id;
        }
        if(!isset($this->id) || $this->id < 1)
        {
            $this->setError('property id does existed or not set in object');
            return null;
        }
        if(!property_exists($this,'is_active'))
        {
            $this->setError('property is_active does existed or not set in object');
            return null;
        }
        return $this->updateQuery("UPDATE $table SET is_active = 0 WHERE id = :id", [':id' => $this->id]);
    }
}
