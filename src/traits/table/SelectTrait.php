<?php
namespace GemFramework\Traits\Table;

trait SelectTrait{

    public function getById(int $id): void
    {
        $table = $this->setTable();
        if(!$table)
        {
            $this->setError('table is not setted in function setTable');
        }
        $row = $this->selectQuery("SELECT * FROM $table WHERE id = :id", [':id' => $id]);
        if (null !== $row && isset($row[0]) && \is_array($row[0])) {
            $this->fetchObject($row[0]);
        } else {
            $this->setError('Object with given id dose not Existed');
        }
    }

    
    /**
     * @param array<int> $ids
     *
     * @return null|array<$this>
     */
    public function ids(array $ids): array|null
    {
        $stringIds = '';
        foreach ($ids as $id) {
            $stringIds .= $id.',';
        }
        $stringIds = rtrim($stringIds, ',');
        $query = "SELECT * FROM {$this->table} WHERE id IN ({$stringIds})";
        $queryResult = $this->selectQuery($query, []);
        if(is_array($queryResult))
        {
            return $this->fetchAllObjects($queryResult);
        }
        return null;
    }


    private function fetchObject(array $row)
    {
        foreach($row as $key => $value)
        {
            if(property_exists($this, $key)){
                $this->$key = $value;
            }
        }
    }

    private function fetchAllObjects(array $rows):array
    {
        $objects = [];
            foreach($rows as $row)
            {
                $obj = new $this;
                $objects[] = $obj->fetchObject($row);
            }
        return $objects;
    }

}