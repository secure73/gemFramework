<?php

namespace Gemvc\Core;

use Exception;
use Gemvc\Http\Request;
use Gemvc\Http\JsonResponse;
use Gemvc\Http\Response;

class Model
{
    public Request $request;
    public ?string $error;
    public function __construct(Request $request)
    {
        $this->request = $request;
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
        /**@phpstan-ignore-next-line*/
        $result_id = $object->insertSingleQuery();
        if(!$result_id)
        {
            return Response::internalError($object->getError());
        }
        $object->id = $result_id;
        return Response::created($object,1,'created successfully');
    }
}
