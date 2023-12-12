<?php
namespace GemFramework\Traits\Model;
use GemLibrary\Http\GemRequest;
use GemLibrary\Http\JsonResponse;

trait DeactivateTrait
{
    public function deactivate(GemRequest $request):JsonResponse
    {
        $jsonResponse = new JsonResponse();
        if(!$request->setPostToObject($this))
        {
            $jsonResponse->badRequest($request->getError());
            return $jsonResponse;
        }
        
        if($this->deactivateQuery())
        {
            $jsonResponse->updated($this,1,'dactivated');
        }
        else
        {
            $jsonResponse->unprocessableEntity('already is deactive');
        }
        return $jsonResponse;
    }
}