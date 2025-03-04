<?php

namespace Gemvc\Core;

use Gemvc\Http\JsonResponse;
use Gemvc\Http\Request;
use Gemvc\Http\Response;

/**
 * @protected  GemLibrary\Http\Request $request
 * @protected  null|string  $error
 * @function   validatePosts(array $post_schema):bool
 */
class Controller
{
    protected Request $request;
    protected ?string $error;

    public function __construct(Request $request)
    {
        $this->error = null;
        $this->request = $request;
    }

    /**
     * @param object $object The object to map the POST data to
     * @info: automatically use $this->request->post to map to Model instance
     */
    public function mapPost(object $object): void
    {
        $name = get_class($object);
        if (!count($this->request->post)) {
            $this->error = 'there is no incoming post detected';
            Response::badRequest("there is no incoming post detected for mappping to $name")->show();
            die();
        }
        foreach ($this->request->post as $postName => $value) {
            try {
                if (property_exists($object, $postName)) {
                    $object->$postName = $value;
                }
            } catch (\Exception $e) {
                $this->error = "post $postName cannot be set because " . $e->getMessage();
                Response::unprocessableEntity("post $postName cannot be set to $name because " . $e->getMessage())->show();
                die();
            }
        }
    }

    /**
     * @param array<string> $postNames  array of incoming post for mapping to Table Object
     * @param Table $object The object to map the POST data to
     * @set $this->error type of ?string in case of exception
     * @info: automatically use $this->request->post map to  Model instance
     */
    public function mapPostManuel(array $postNames, Table $object): void
    {
        $objectClassName = get_class($object);
        foreach ($postNames as $name) {
            if (!isset($this->request->post[$name])) {
                $this->error = "there is no post found in incoming request with given name $name";
                Response::badRequest("post $name not setted on incoming request to set on $objectClassName")->show();
                die();
            }
            try {
                if (property_exists($object, $name)) {
                    $object->$name = $this->request->post[$name];
                } else {
                    $this->error = "object $objectClassName has no such property with name $name";
                    Response::unprocessableEntity("object $objectClassName has no such property with name $name")->show();
                    die();
                }
            } catch (\Exception $e) {
                $this->error = "post $name cannot be set to $objectClassName because " . $e->getMessage();
                Response::unprocessableEntity("post $name cannot be set to $objectClassName because " . $e->getMessage())->show();
                die();
            }
        }
    }

    /**
     * columns "id,name,email" only return id name and email
     * @param object $model
     * @param string|null $columns
     * @return JsonResponse
     */
    public function createList(object $model, ?string $columns = null): JsonResponse
    {
        //if model dont have select method throw error
        if (!method_exists($model, 'select')) {
            return Response::internalError('Model must have select method i.e extended from Table or Model Class');
        }
        $model = $this->_handleSearchable($model);
        $model = $this->_handleFindable($model);
        $model = $this->_handleSortable($model);
        $model = $this->_handlePagination($model);
        return Response::success($model->select($columns)->run(), $model->getTotalCounts(), 'list of ' . $model->getTable() . ' fetched successfully');
    }





    /**
     * Validates that required properties are set
     * @throws \RuntimeException
     */
    protected function validateRequiredProperties(): void
    {
        if (!method_exists($this, 'getTable') || empty($this->getTable())) {
            throw new \RuntimeException('Table name must be defined in the model');
        }
    }

    /**
     * Handles pagination parameters
     */
    private function _handlePagination(object $model): object
    {
        if (isset($this->request->get["page_number"])) {
            if (!is_numeric(trim($this->request->get["page_number"]))) {
                Response::badRequest("page_number shall be type if integer or number")->show();
                die();
            }
            $page_number = (int) $this->request->get["page_number"];
            if ($page_number < 1) {
                Response::badRequest("page_number shall be positive int")->show();
                die();
            }

            $model->setPage($page_number);
            return $model;
        }
        $model->setPage(1);
        return $model;
    }


    /**
     * Handles sorting/ordering parameters
     */
    private function _handleSortable(object $model): object
    {
        $sort_des = $this->request->getSortable();
        $sort_asc = $this->request->getSortableAsc();
        if ($sort_des) {
            $model->orderBy($sort_des);
        }
        if ($sort_asc) {
            $model->orderBy($sort_asc, true);
        }
        return $model;
    }


    private function _handleFindable(object $model): object
    {
        $array_orderby = $this->request->getFindable();
        if (count($array_orderby) == 0) {
            return $model;
        }
        foreach ($array_orderby as $key => $value) {
            $array_orderby[$key] = $this->_sanitizeInput($value);
        }
        $array_exited_object_properties = get_class_vars(get_class($model));
        foreach ($array_orderby as $key => $value) {
            if (!array_key_exists($key, $array_exited_object_properties)) {
                Response::badRequest("filterable key $key not found in object properties")->show();
                die();
            }
        }
        foreach ($array_orderby as $key => $value) {
            $model->whereLike($key, $value);
        }
        return $model;
    }


    /**
     * Handles all filter types (create where)
     */
    private function _handleSearchable(object $model): object
    {
        $arr_errors = null;
        $array_searchable = $this->request->getFilterable();
        if (count($array_searchable) == 0) {
            return $model;
        }
        foreach ($array_searchable as $key => $value) {
            $array_searchable[$key] = $this->_sanitizeInput($value);
        }
        $array_exited_object_properties = get_class_vars(get_class($model));
        foreach ($array_searchable as $key => $value) {
            if (!array_key_exists($key, $array_exited_object_properties)) {
                Response::badRequest("searchable key $key not found in object properties")->show();
                die();
            }
        }

        foreach ($array_searchable as $key => $value) {
            try {
                $model->$key = $value;
            } catch (\Exception $e) {
                $arr_errors .= $e->getMessage() . ",";
            }
        }

        if ($arr_errors) {
            Response::badRequest($arr_errors)->show();
            die();
        }
        foreach ($array_searchable as $key => $value) {
            $model->where($key, $value);
        }
        return $model;
    }


    /**
     * Basic input sanitization
     */
    private function _sanitizeInput(mixed $input): mixed
    {
        if (is_string($input)) {
            // Remove any null bytes
            $input = str_replace(chr(0), '', $input);
            // Convert special characters to HTML entities
            return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        }
        return $input;
    }
}
