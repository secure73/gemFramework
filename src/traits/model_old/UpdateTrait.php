<?php
namespace GemFramework\Traits\Model;
use GemLibrary\Http\GemRequest;
use GemLibrary\Http\JsonResponse;
trait UpdateTrait
{

    public function update(GemRequest $request):JsonResponse
    {
        $jsonResponse = new JsonResponse();
        if(!$request->setPostToObject($this))
        {
            $jsonResponse->badRequest($request->getError());
            return $jsonResponse;
        }
        
        if($this->updateSingleQuery())
        {
            $jsonResponse->updated($this,1,'updated successfully');
        }
        else
        {
            $jsonResponse->unprocessableEntity('nothing to update');
        }
        return $jsonResponse;
    }
}
