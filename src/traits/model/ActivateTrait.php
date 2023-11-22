<?php
namespace GemFramework\Traits\Model;

use GemFramework\Traits\Table\ActivateQueryTrait;
use GemLibrary\Http\GemRequest;
use GemLibrary\Http\JsonResponse;

trait ActivateTrait
{
    use ActivateQueryTrait;
    public function activate(GemRequest $request):JsonResponse
    {
        $jsonResponse = new JsonResponse();
        if(!$request->setPostToObject($this))
        {
            $jsonResponse->badRequest($request->getError());
            return $jsonResponse;
        }
        
        if($this->activateQuery())
        {
            $jsonResponse->updated($this,1,'activated successfully');
        }
        else
        {
            $jsonResponse->badRequest($this->getError());
        }
        return $jsonResponse;
    }
}