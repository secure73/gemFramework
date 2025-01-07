<?php
namespace Gemvc\Traits\Model;
use Gemvc\Http\Response;
use Gemvc\Http\JsonResponse;
use Gemvc\Traits\Table\UpdateQuery;

/**
 * Summary of UpdateTrait
 * @method update()
 * @method updateJsonResponse()
 */
trait UpdateTrait
{
    use UpdateQuery;


    public function updateJsonResponse():JsonResponse
    {
        $result = $this->update();
        if($result === null)
        {
            return Response::internalError("error in update Query: ".$this->getTable().": ".$this->getError());
        }
        return Response::updated($result,1,"updated successfully");
    }
}
