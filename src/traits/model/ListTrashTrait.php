<?php
namespace GemFramework\Traits\Model;
use GemLibrary\Http\GemRequest;
use GemLibrary\Http\JsonResponse;
trait ListTrashTrait
{

    public function trash(GemRequest $request):JsonResponse
    {
        if(isset($request->post['page']))
        {
            $this->setPage((int)$request->post['page']);
        }
        if(isset($request->post['orderby']))
        {
            $this->setOrderBy($request->post['orderby']);
        }
        if(isset($request->post['find']))
        {
            $this->setFind($request->post['find']);
        }
        if(isset($request->post['between']))
        {
            $this->setBetween($request->post['between']);
        }
        if(isset($request->post['where']))
        {
            $this->setWhere($request->post['where']);
        }
        $jsonResponse = new JsonResponse();
        if(!$this->getError())
        {
            $result = $this->ListTrashQuery();
            $jsonResponse->success( $result,$this->getCount());
        }
        else
        {
            $jsonResponse->badRequest($this->getError());
        }
        return $jsonResponse;
    }
}
