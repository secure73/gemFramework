<?php

namespace Gemvc\Traits\Model;

use Gemvc\Http\JsonResponse;

trait CreateTrait
{
    public function create(): self|false
    {

        $table = $this->setTable();
        if (!$table) {
            $this->setError('Table is not set in function setTable');
            return false;
        }

        $columns = '';
        $params = '';
        $arrayBind = [];
        $query = "INSERT INTO {$table} ";
        foreach ((object) $this as $key => $value) {
            $columns .= $key . ',';
            $params .= ':' . $key . ',';
            $arrayBind[':' . $key] = $value;
        }

        $columns = rtrim($columns, ',');
        $params = rtrim($params, ',');

        $query .= " ({$columns}) VALUES ({$params})";
        $id = $this->insertQuery($query, $arrayBind);

        if (!$id) {
          return false;
        } 
        $this->id = $id;
        return $this;
    }

    public function createWithJsonResponse():JsonResponse
    {
        $result = $this->create();
        if ($result === false) {
            return JsonResponse::internalError('Error in create query: ' . $this->getError());
        }
        return JsonResponse::created($result,1, 'Object created successfully');
    }

    
}
