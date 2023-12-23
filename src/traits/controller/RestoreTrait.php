<?php
namespace GemFramework\Traits\Controller;

use GemLibrary\Http\JsonResponse;

trait RestoreTrait{
    public function restore(): JsonResponse
    {
        if(!$this->request->definePostSchema(['id' => 'int'])){
            $this->response->badRequest($this->request->error);
            return $this->response;
        }
        return $this->model->restore($this->request);
    }
}