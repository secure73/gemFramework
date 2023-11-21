<?php
namespace GemFramework\Traits\Table;

/**
 * this Trait deliver tow methods:
 * delete():int|null
 * restore():int|null
 * the method update deleted_at to null or timestamp
 * @method delete()
 * @method restore()
 */
trait SafeDeleteTrait
{  
    /**
     * @param int|null $id
     * @return int|null
     * @description : if id = null then use $this->id
     * return 1 if successful and null if not
     */
     public function safeDelete(int $id = null): int|null
    {
        $table = $this->setTable();
        if(!$table)
        {
            $this->setError('table is not setted in function setTable');
            return null;
        }
        if (!$id) {
            $id = $this->id;
        }
        if(!$id || $id < 1)
        {
            $this->setError('property id does existed or not setted in object');
            return null;
        }
        $query = "UPDATE $table SET deleted_at = NOW() WHERE id = :id";

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
            $this->setError('table is not setted in function setTable');
            return null;
        }
        if (!$id) {
            $id = $this->id;
        }
        if(!$id || $id < 1)
        {
            $this->setError('property id does existed or not setted in object');
            return null;
        }
        $query = "UPDATE {$this->table} SET deleted_at = NULL WHERE id = :id";

        return $this->updateQuery($query, []);
    }
}
