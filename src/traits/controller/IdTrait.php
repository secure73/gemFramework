<?php
namespace GemFramework\Traits\Controller;

use GemLibrary\Http\JsonResponse;

/**
 * @method id()
 * @return JsonResponse
 */
trait IdTrait
{
    public function id(): JsonResponse
    {
        if(!$this->request->definePostSchema(['id' => 'int'])){
            $this->response->badRequest($this->request->error);
            return $this->response;
        }
        return $this->model->id($this->request);
    }
}