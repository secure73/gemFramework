<?php
namespace GemFramework\Traits\Model;
use GemLibrary\Http\GemRequest;
use GemLibrary\Http\JsonResponse;

trait CreateTrait
{
    public function create(GemRequest $request):JsonResponse
    {
        $jsonResponse = new JsonResponse();
        if(!$request->setPostToObject($this))
        {
            $jsonResponse->badRequest($request->getError());
            return $jsonResponse;
        }
        $id = $this->insertSingleQuery();
        if($id)
        {
            $jsonResponse->success($id,null,'created successfully');
        }
        else
        {
            $jsonResponse->badRequest($this->getError());
        }
        return $jsonResponse;
    }
}
