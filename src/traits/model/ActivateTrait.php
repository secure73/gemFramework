<?php
namespace Gemvc\Traits\Model;

use Gemvc\Traits\Table\ActivateQuery;
use Gemvc\Http\JsonResponse;
use Gemvc\Http\Response;
use Gemvc\Core\Table;
use Gemvc\Traits\Table\DeactivateQuery;

trait ActivateTrait
{
    use ActivateQuery;
    use DeactivateQuery;
    public function activate(Table $instanceTable):JsonResponse
    {
        if(!isset($this->request->post['id'])|| !$this->request->post['id'] || !is_integer( $this->request->post['id']))
        {
            return Response::unprocessableEntity('post id not found or contain none numeric inhalt');
        }
         $this->request->post['id'];
        
        if(!$this->activateQuery($this->request->post['id']))
        {
            return Response::internalError($instanceTable->getError());
        }
        
        return Response::success($instanceTable,1);
    }

    public function deactivate(Table $instanceTable):JsonResponse
    {
        if(!isset($this->request->post['id'])|| !$this->request->post['id'] || !is_integer( $this->request->post['id']))
        {
            return Response::unprocessableEntity('post id not found or contain none numeric inhalt');
        }
         $this->request->post['id'];
        
        if(!$this->deactivateQuery($this->request->post['id']))
        {
            return Response::internalError($instanceTable->getError());
        }
        
        return Response::success($instanceTable,1);
    }
}