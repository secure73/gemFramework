<?php
namespace Gemvc\Traits\Model;

use Gemvc\Traits\Table\RemoveQuery;
use Gemvc\Http\Request;
use Gemvc\Http\JsonResponse;
use Gemvc\Http\Response;
trait RemoveTrait
{
    use RemoveQuery;
    public function removeById(int $id):bool
    {
        if(!$this->removeQuery($id))
        {
           return false;
        }
        return true;
    }

    public function removeByPostRequest(Request $request):JsonResponse
    {
        if(!$request->definePostSchema(['id' => 'int']))
        {
            return Response::badRequest($request->error);
        }
        if( !$this->removeById($request->post["id"]))
        {
           return  Response::internalError("error in remove query: ".$this->getTable()." , ".$this->getError());
        }
        return Response::deleted($request->post["id"],1,"removed successfully");
    }
}
