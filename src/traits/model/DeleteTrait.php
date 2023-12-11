<?php
namespace GemFramework\Traits\Model;

use GemFramework\Traits\Table\UpdateQueryTrait;
use GemLibrary\Http\GemRequest;
use GemLibrary\Http\JsonResponse;

trait DeleteTrait
{
    use UpdateQueryTrait;
    public function delete(GemRequest $request):JsonResponse
    {
        $jsonResponse = new JsonResponse();
        if(!isset($request->post['id']))
        {
            $jsonResponse->badRequest('post id is not setted');
            return $jsonResponse;
        }
        if(!is_numeric($request->post['id']) || $request->post['id'] < 1)
        {
            $jsonResponse->badRequest('id is not nummeric or less than 1');
            return $jsonResponse;
        }
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
