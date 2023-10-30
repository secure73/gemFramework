<?php
namespace App\Controller;
use App\Model\UserModel;
use GemFramework\Core\Controller;
use GemLibrary\Http\GemRequest;
use GemLibrary\Http\JsonResponse;
class AuthController extends Controller{

    public function __construct(GemRequest $request){
        parent::__construct($request);
    }


    public function index(): JsonResponse{
        $userModel = new UserModel();
        $userModel->create();
        


        $this->response->success([]);
        return $this->response;
    }
}