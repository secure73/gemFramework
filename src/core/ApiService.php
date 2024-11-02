<?php

namespace Gemvc\Core;

use Gemvc\Http\Request;
use Gemvc\Http\Response;
use Gemvc\Http\JsonResponse;


/**
 * @function auth(string $role = null):bool
 * @property Request $request
 * public service is suitable for all service without need of Authentication, like Login , Register etc...
 */
class ApiService
{
    protected Request $request;
    public ?string $error;

    public function __construct(Request $request)
    {
        $this->error = null;
        $this->request = $request;
    }

    public function index(): JsonResponse
    {
        $name = get_class($this);
        //because get_class return class name with namespace like App\\Service\\className ,
        //we need only to shoe className and it is in index 2
        $name = explode('\\', $name)[2];
        return Response::success("welcome to $name service");
    }
}
