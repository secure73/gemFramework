<?php
namespace App\Controller;
use GemFramework\Core\Controller;
use GemLibrary\Http\GemRequest;
use GemLibrary\Http\JsonResponse;
class AuthController extends Controller{

    public function __construct(GemRequest $request){
        parent::__construct($request);
    }


    public function index(): JsonResponse{
        $this->response->success([]);

        $this->response->show();
        return $this->response;
    }
}