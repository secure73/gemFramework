<?php
namespace GemFramework\Traits\Model;

use GemFramework\Traits\Table\SafeDeleteQueryTrait;
use GemLibrary\Http\GemRequest;
use GemLibrary\Http\JsonResponse;

trait DeleteTrait
{
    use SafeDeleteQueryTrait;
    public function delete(GemRequest $request):JsonResponse
    {
        $jsonResponse = new JsonResponse();
        if(!$request->setPostToObject($this))
        {
            $jsonResponse->badRequest($request->getError());
            return $jsonResponse;
        }
        
        if($this->safeDeleteQuery($this->id))
        {
            $jsonResponse->success($this,1,'deleted successfully');
        }
        else
        {
            $jsonResponse->badRequest($this->getError());
        }
        return $jsonResponse;
    }
}
