<?php
namespace GemFramework\Traits\Controller;

use GemLibrary\Http\JsonResponse;

/**
 * @method deactivate()
 * @return JsonResponse
 */
trait DeactivateTrait
{
    public function deactivate(): JsonResponse
    {
        if(!$this->request->definePostSchema(['id' => 'int'])){
            $this->response->badRequest($this->request->error);
            return $this->response;
        }
        return $this->model->deactivate($this->request);
    }
}