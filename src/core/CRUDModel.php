<?php

namespace Gemvc\Core;

use Exception;
use Gemvc\Http\Request;
use Gemvc\Http\JsonResponse;
use Gemvc\Http\Response;

class CRUDModel
{
    public Request $request;
    public ?string $error;
    public Table $table;
    public function __construct(Request $request, Table $table)
    {
        $this->request = $request;
        $this->table = $table;
    }

    /**
     * @param Table $object The object to map the POST data to
     * @return bool True on success, false on failure with an error message set in `$this->error`
     * @set $this->error type of ?string in case of exception
     * @info: automatically use $this->request->post from Model instance
     */
    public function mapPost(Table $object): bool
    {
        if (!is_array($this->request->post) || !count($this->request->post)) {
            $this->error = 'there is no incoming post detected';
            return false;
        }
        foreach ($this->request->post as $postName => $value) {
            try {
                if (property_exists($object, $postName)) {
                    $object->$postName = $value;
                }
            } catch (Exception $e) {
                $this->error = "post $postName cannot be set because " . $e->getMessage();
                return false;
            }
        }
        return true;
    }

    /**
     * @param array<string> $postNames  array of incoming post for mapping to Table Object
     * @param Table $object The object to map the POST data to
     * @return bool True on success, false on failure with an error message set in `$this->error`
     * @set $this->error type of ?string in case of exception
     * @info: automatically use $this->request->post from Model instance
     */
    public function mapPostManuel(array $postNames , Table $object): bool
    {
        foreach($postNames as $name)
        {
            if(!isset($this->request->post[$name]))
            {
                $this->error = "there is no post found in incoming request with given name $name";
                return false;
            }
            try {
                if (property_exists($object, $name)) {
                    $object->$name =$this->request->post[$name];
                }
                else
                {
                    $this->error = "object has no such property $name";
                    return false;
                }
            } catch (Exception $e) {
                $this->error = "post $name cannot be set because " . $e->getMessage();
                return false;
            }
        }
        return true;
    }
    /**
     * depends on InsertSingleQueryTrait on Table!
     * @param \Gemvc\Core\Table $object
     * @return \Gemvc\Http\JsonResponse
     */
    public function createSingle(Table $object):JsonResponse
    {
        $this->mapPost($object);
        if(!method_exists($object,'insertSingleQuery'))
        {
            return Response::internalError('there is no use InsertSingleQuery Traits defined');
        }
        $result_id = $object->insertSingleQuery();
        if(!$result_id)
        {
            return Response::internalError($object->getError());
        }
        // @phpstan-ignore-next-line
        $object->id = $result_id;
        return Response::created($object,1,'created successfully');
    }

    /**
     * depends on UpdateSingleQueryTrait on Table!
     * @param \Gemvc\Core\Table $object
     * @return \Gemvc\Http\JsonResponse
     */
    public function updateSingle(Table $object):JsonResponse
    {
        if(!isset($this->request->post['id']))
        {
            return Response::notAcceptable('post id is required');
        }
        // @phpstan-ignore-next-line
        $id = (int)$this->request->post['id'];
        $find_object = $object->select()->where('id',$id)->limit(1)->run();
        if(!is_array($find_object) || count($find_object) !== 1)
        {
            return Response::notFound('there is no object found with given id');
        }
        $find_object = $find_object[0];
        $this->mapPost($find_object);

        if(!method_exists($object,'updateSingleQuery'))
        {
            return Response::internalError('there is no use updateSingleQuery Traits defined');
        }
        /**@phpstan-ignore-next-line*/
        if(!$find_object->updateSingleQuery())
        {
            return Response::unprocessableEntity('nothing to update');
        }
        return Response::success($find_object,1,'updated successfully');
    }
}
