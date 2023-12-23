<?php
namespace GemFramework\Core;
use GemLibrary\Http\GemRequest;
use GemLibrary\Http\JsonResponse;
use GemFramework\Traits\Controller\IdTrait;
use GemFramework\Traits\Controller\ListTrait;
use GemFramework\Traits\Controller\RestoreTrait;
use GemFramework\Traits\Controller\TrashTrait;
use GemFramework\Traits\Controller\DeleteTrait;

class Controller {
    protected JsonResponse $response;
    protected GemRequest $request;
    public object $model;

    use IdTrait;
    use RestoreTrait;
    use ListTrait;
    use DeleteTrait;
    use TrashTrait;

    public function __construct(GemRequest $request , object $model) {
        $this->request = $request;
        $this->model = $model;
        $this->response = new JsonResponse();
    }
}
