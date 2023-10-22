<?php
namespace GemFramework\Traits\Table;

trait SelectTrait{

    public function getById(int $id): null
    {
        $table = $this->setTable();
        if(!$table)
        {
            $this->setError('table is not setted in function setTable');
            return null;
        }
        $row = $this->selectQuery("SELECT * FROM $table WHERE id = :id", [':id' => $id]);
        if (null !== $row && isset($row[0]) && \is_array($row[0])) {
            $this->fetchAllObjects($row[0]);
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

        return $this->selectQuery($query, []);
    }
}