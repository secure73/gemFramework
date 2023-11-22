<?php
namespace AppTraits\Model;
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
        
        if($this->updateSingle())
        {
            $jsonResponse->updated($this,1,'updated successfully');
        }
        else
        {
            $jsonResponse->badRequest($this->getError());
        }
        return $jsonResponse;
    }
}