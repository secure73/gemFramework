<?php
namespace GemFramework\Traits\Model;

use GemLibrary\Http\GemRequest;
use GemLibrary\Http\JsonResponse;

trait RestoreTrait
{
    public function restore(GemRequest $request):JsonResponse
    {
        $jsonResponse = new JsonResponse();
        if(!$request->setPostToObject($this))
        {
            $jsonResponse->badRequest($request->getError());
            return $jsonResponse;
        }
        
        $table = $this->setTable();
        if(!$table)
        {
            $jsonResponse->internalError('table is not setted in function setTable');
            return $jsonResponse;
        }
        if(!$this->id || $this->id < 1)
        {
            $jsonResponse->badRequest('property id does existed or not setted in object');
            return $jsonResponse;
        }
        $query = "UPDATE {$this->setTable()} SET deleted_at = NULL WHERE id = :id";

        if($this->updateQuery($query, [':id' => $this->id]))
        {
            $jsonResponse = new JsonResponse();
            $jsonResponse->success($this->id, 1,'restored');
        }
        else
        {
            $jsonResponse->internalError($this->getError());
        }
        return $jsonResponse;
    }
}
