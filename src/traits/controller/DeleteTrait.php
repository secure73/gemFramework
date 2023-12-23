<?php
namespace GemFramework\Traits\Controller;
use GemLibrary\Http\JsonResponse;

trait DeleteTrait
{
    public function delete(): JsonResponse
    {
        if(!$this->request->definePostSchema(['id' => 'int'])){
            $this->response->badRequest($this->request->error);
            return $this->response;
        }
        return $this->model->delete($this->request);
    }
}