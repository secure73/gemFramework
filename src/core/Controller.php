<?php
namespace GemFramework\Core;
use GemLibrary\Http\GemRequest;
use GemLibrary\Http\JsonResponse;

class Controller {
    protected JsonResponse $response;
    protected GemRequest $request;
    
    public $model;/** @phpstan-ignore-line */

    public function __construct(GemRequest $request) {
        $this->request = $request;
        $this->response = new JsonResponse();
    }
}
?>
