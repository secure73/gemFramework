<?php

namespace Gemvc\Traits\Table;
use Gemvc\Http\Response;

/**
 * @method selectByIdQuery()
 * @method selectByIdsQuery()
 * select object with given id or array of objects by giving ids
 */
trait SelectQueryTrait
{
    /**
     * @param int|null $id
     * @return null|object<$this>
     * Set $this value and return $this if found, null otherwise
     */
    public function selectByIdQuery(?int $id = null): null|object
    {
        $table = $this->getTable();
        if (!$table) {
            $this->setError('Table is not set in function getTable');
            Response::internalError($this->getError())->show();
            die();
        }
        if ($id) {
            $this->id = $id;
        }
        if (!isset($this->id) || $this->id < 1) {
            $this->setError($this->getTable().": Property id  is not set in object");
            Response::internalError($this->getError())->show();
            die();
        }

        $select_result =  $this->selectQuery("SELECT * FROM {$table} WHERE id = :id LIMIT 1", [':id' => $id]);
        if($this->getError() || $select_result == false)
        {
            $this->setError($this->getTable().": Failed to select row from table : ".$this->getError());
            Response::internalError($this->getError())->show();
            die();
        }

        if(count($select_result) == 0)
        {
            return null;
        }
        $select_result = $select_result[0];
        foreach ($select_result as $key => $value) {
            $this->$key = $value;
        }
        return $this;
    }

    /**
     * @param array<int> $ids
     * @return null|array<$this>
     */
    public function selectByIdsQuery(array $ids): array
    {
        $table = $this->getTable();
        if (!$table) {
            $this->setError('table is not set in function getTable');
            Response::internalError($this->getError())->show();
            die();
        }
        if (count($ids) == 0) {
            $this->setError('ids array is empty');
            Response::internalError($this->getError())->show();
            die();
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $query = "SELECT * FROM {$table} WHERE id IN ({$placeholders})";

        $result = $this->selectQueryObjets($query, []);
        if ($this->getError() || $result === false) {
            $this->setError($this->getTable().": Failed to select rows from table:". $this->getError());
            Response::internalError($this->getError())->show();
            die();
        }
        if(count($result) < 1)
        {
            $this->setError('No rows found within ids:'.implode(',',$ids));
            return [];
        }
        return $result;
    }
}
