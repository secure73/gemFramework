<?php
namespace GemFramework\Core;
use GemLibrary\Http\GemRequest;
use GemLibrary\Http\JsonResponse;
class Controller {
    protected JsonResponse $response;
    protected GemRequest $request;
    public function __construct(GemRequest $request) {
        $this->response = new JsonResponse();
        $this->request = $request;
    }
}