<?php
namespace GemFramework\Traits\Controller;

use GemLibrary\Http\JsonResponse;

/**
 * @method list()
 * @return JsonResponse
 */
trait ListTrait{

    public function list():JsonResponse
    {
        if(!$this->request->definePostSchema(['?page' => 'int' , '?orderby' => 'string' , '?find' => 'string' , '?between' => 'string' , '?where' => 'string'])){
            $this->response->badRequest($this->request->error);
            return $this->response;
        }
        return $this->model->list($this->request);
    }
}
