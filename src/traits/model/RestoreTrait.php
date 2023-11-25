<?php
namespace GemFramework\Traits\Model;

use GemFramework\Traits\Table\SafeDeleteQueryTrait;
use GemLibrary\Http\GemRequest;
use GemLibrary\Http\JsonResponse;

trait RestoreTrait
{
    use SafeDeleteQueryTrait;
    public function restore(GemRequest $request):JsonResponse
    {
        $jsonResponse = new JsonResponse();
        if(!$request->setPostToObject($this))
        {
            $jsonResponse->badRequest($request->getError());
            return $jsonResponse;
        }
        
        if($this->safeDeleteQuery($this->id))
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
