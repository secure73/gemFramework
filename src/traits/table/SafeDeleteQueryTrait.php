<?php
namespace GemFramework\Traits\Table;

/**
 * @method safeDeleteQuery()
 * @method restoreQuery()
 * method update deleted_at to null or timestamp
 */
trait SafeDeleteQueryTrait
{  
    /**
     * @param int|null $id
     * @return int|null
     * @description : if id = null then use $this->id
     * return 1 if successful and null if not
     */
     public function safeDeleteQuery(int $id = null): int|null
    {
        $table = $this->setTable();
        if(!$table)
        {
            $this->setError('table is not set in function setTable');
            return null;
        }
        if ($id) {
            $this->id = $id;
        }
        if(!$id || $id < 1)
        {
            $this->setError('property id does existed or not set in object');
            return null;
        }
        $query = "UPDATE $table SET deleted_at = NOW()  WHERE id = :id";
        if(property_exists($this,'is_active'))
        {
            $query = "UPDATE $table SET deleted_at = NOW(), is_active = 0  WHERE id = :id";
        }
        return $this->updateQuery($query, [':id' => $id]);
    }

    /**
     * @param int|null $id
     * @return int|null
     * @description : if id = null then use $this->id
     * return 1 if successful and null if not
     */
    public function restoreQuery(int $id = null): int|null
    {
        $table = $this->setTable();
        if(!$table)
        {
            $this->setError('table is not set in function setTable');
            return null;
        }
        if ($id) {
    	    $this->id = $id;
        }
        if(!$id || $id < 1)
        {
            $this->setError('property id does existed or not set in object');
            return null;
        }
        $query = "UPDATE {$this->table} SET deleted_at = NULL WHERE id = :id";

        return $this->updateQuery($query, [':id' => $id]);
    }
}
