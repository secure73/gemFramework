<?php
namespace GemFramework\Traits\Model;

use GemFramework\Traits\Table\RemoveQueryTrait;
use GemLibrary\Http\GemRequest;
use GemLibrary\Http\JsonResponse;

trait RemoveTrait
{
    use RemoveQueryTrait;
    public function remove(GemRequest $request):JsonResponse
    {
        $jsonResponse = new JsonResponse();
        if(!isset($request->post['id']) || !is_numeric($request->post['id']) || $request->post['id'] < 1)
        {
            $jsonResponse->badRequest('id is not valid');
            return $jsonResponse;
        }
        $this->id = $request->post['id'];
        if($this->deleteSingleQuery())
        {
            $jsonResponse->success(null,null,'deleted successfully');
        }
        else
        {
            $jsonResponse->badRequest($this->getError());
        }
        return $jsonResponse;
    }
}
