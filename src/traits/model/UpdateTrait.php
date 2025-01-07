<?php
namespace Gemvc\Traits\Model;
use Gemvc\Http\Response;
use Gemvc\Http\JsonResponse;
use Gemvc\Traits\Table\UpdateQuery;

/**
 * Summary of UpdateTrait
 * @method updateSingleQuery()
 * @method setNullQuery()
 * @method setTimeNowQuery()
 * @method updateQuery()
 * @method update()
 * @method updateJsonResponse()
 */
trait UpdateTrait
{
    use UpdateQuery;

    public function update():static|null
    {       
        if($this->updateSingleQuery())
        {
            return $this;
        }
        return null;
        
    }

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
