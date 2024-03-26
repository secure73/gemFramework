<?php

namespace GemFramework\Traits\Model;

use GemLibrary\Http\GemRequest;
use GemLibrary\Http\JsonResponse;

trait CreateTrait
{
    public function create(GemRequest $request): JsonResponse
    {
        $jsonResponse = new JsonResponse();
        if (!$request->setPostToObject($this)) {
            $jsonResponse->badRequest($request->getError());
            return $jsonResponse;
        }


        $table = $this->setTable();
        if (!$table) {
            $jsonResponse->internalError('Table is not set in function setTable');
            return $jsonResponse;
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

        if ($id) {
            $jsonResponse->success($id, null, 'created successfully');
        } else {
            $jsonResponse->badRequest($this->getError());
        }
        return $jsonResponse;
    }
}
