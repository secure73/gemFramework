<?php
namespace Gemvc\Traits\Model;

use Gemvc\Http\JsonResponse;
use Gemvc\Http\Response;

/**
 * @method id(int $id):self|null
 * @method byId(int $id):self
 * @method findOrFail(int $id):self
 * @method singleJson(int $id):JsonResponse
 */
trait IdTrait
{
    /**
     * @param int $id
     * @return self|null
     */
    public function id(int $id):self|null
    {
        $found = $this->select()->where('id', $id)->limit(1)->run();
        if(count($found) == 0)
        {
            return null;
        }
        return $found[0];
    }


    /**
     * @param int $id
     * @return self|null
     */
    public function byId(int $id):self|null
    {
        return $this->id($id);
    }

    /**
     * return self or show JsonResponse 404 error if not found
     * @param int $id
     * @return self
     */
    public function findOrFail(int $id): self
    {
        $result = $this->byId($id);
        if(!$result)
        {
            Response::notFound(get_class($this).' not found with given id: '.$id)->show();
            die();
        }
        return $result;
    }


    /**
     * return a single row as a json response, with success status and message or error status 404 and message
     * @param int $id
     * @return JsonResponse
     */ 
    public function singleJson(int $id): JsonResponse
    {
        return Response::success($this->byId($id),1,'success');
    }
}
