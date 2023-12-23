<?php
namespace GemFramework\Traits\Model;

use GemLibrary\Http\GemRequest;
use GemLibrary\Http\JsonResponse;

/**
 * @method id(GemRequest $request)
 * @return JsonResponse
 */
trait IdTrait
{
    public function id(GemRequest $request):JsonResponse
    {
        $jsonResponse = new JsonResponse();
        if(!isset($request->post['id']) || !is_numeric($request->post['id']) || $request->post['id'] < 1)
        {
            $jsonResponse->badRequest('id '.$request->post['id'].' is not valid');
            return $jsonResponse;
        }
        $id = (int)$request->post['id'];
        $found = $this->selectById($id);
        if($found === false)
        {
            $jsonResponse->internalError($this->getError());
            return $jsonResponse;
        }
        if($found === null)
        {
            $jsonResponse->notFound('id '.$id.' is not found');
            return $jsonResponse;
        }
        $jsonResponse->success($found);
        return $jsonResponse;
    }
}
