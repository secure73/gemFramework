<?php
namespace AppTraits\Model;
use GemLibrary\Http\GemRequest;
use GemLibrary\Http\JsonResponse;
use GemFramework\Traits\Table\SafeDeleteTrait;

trait RestoreTrait
{
    use SafeDeleteTrait;
    public function restore(GemRequest $request):JsonResponse
    {
        $jsonResponse = new JsonResponse();
        if(!$request->setPostToObject($this))
        {
            $jsonResponse->badRequest($request->getError());
            return $jsonResponse;
        }
        
        if($this->safeDelete($this->id))
        {
            $jsonResponse->updated($this,1,'restored successfully');
        }
        else
        {
            $jsonResponse->badRequest($this->getError());
        }
        return $jsonResponse;
    }
}