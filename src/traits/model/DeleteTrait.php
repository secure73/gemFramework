<?php
namespace GemFramework\Traits\Model;
use GemLibrary\Http\GemRequest;
use GemLibrary\Http\JsonResponse;
use GemFramework\Traits\Table\SafeDeleteTrait;

trait DeleteTrait
{
    use SafeDeleteTrait;
    public function delete(GemRequest $request):JsonResponse
    {
        $jsonResponse = new JsonResponse();
        if(!$request->setPostToObject($this))
        {
            $jsonResponse->badRequest($request->getError());
            return $jsonResponse;
        }
        
        if($this->safeDelete($this->id))
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