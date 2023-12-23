<?php
namespace GemFramework\Core;
use GemLibrary\Http\GemRequest;
use GemLibrary\Http\JsonResponse;
class Controller {
    protected JsonResponse $response;
    protected GemRequest $request;
    public mixed $model;
    public function __construct(GemRequest $request , mixed $model) {
        $this->request = $request;
        $this->model = $model;
        $this->response = new JsonResponse();
    }
}
