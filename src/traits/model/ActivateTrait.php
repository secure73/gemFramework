<?php
namespace Gemvc\Traits\Model;

use Gemvc\Traits\Table\ActivateQueryTrait;
use Gemvc\Http\JsonResponse;
use Gemvc\Http\Response;
use Gemvc\Core\Table;

trait ActivateTrait
{
    use ActivateQueryTrait;
    public function activate(Table $instanceTable):JsonResponse
    {
        if(!isset($this->request->post['id'])|| !$this->request->post['id'] || !is_integer( $this->request->post['id']))
        {
            return Response::unprocessableEntity('post id not found or contain none numeric inhalt');
        }
        $instanceTable->id = $this->request->post['id'];
        
        if(!$instanceTable->activateQuery())
        {
            return Response::internalError($instanceTable->getError());
        }
        
        return Response::success($instanceTable,1);
    }
}