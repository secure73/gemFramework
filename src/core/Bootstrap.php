<?php
namespace GemFramework\Core;

use Gemvc\Http\GemRequest;
use Gemvc\Http\JsonResponse;

class Bootstrap {

    public GemRequest   $gemRequest;
    public ?object      $service;
    public string       $controller;
    public string       $method;
    public bool         $isServiceExists;
    public bool         $isMethodExists;
    public ?string      $error;
    public JsonResponse $response;
    public function __construct(GemRequest $gemRequest)
    {
        $this->gemRequest = $gemRequest;

        $segments = explode($gemRequest->requestedUrl, '/');
        $this->controller = ucfirst($segments[1]);
        isset($segments[2]) ? $this->method = $segments[2] : $this->method = 'index';
       
        $this->isServiceExists  = false;
        $this->isMethodExists = false;
    }

    public function runApp():JsonResponse
    {
        if($this->makeInstanceService())
        {
            if($this->isFunctionExists())
            {
                $res = $this->service->method();
                if(!$res instanceof JsonResponse)
                {
                    $this->response->internalError('Method ' . $this->controller.'/'.$this->method . 'dosent return Object of JsonResponse' );
                }
                return $this->response;
            }
        }
        return $this->response;
    }

    private function makeInstanceService(): bool
    {
        try {
            $service = 'App\\Controller\\' . $this->controller;
            $this->service = new $service($this->gemRequest);
            $this->isServiceExists = true;
            return true;
        } catch (\Throwable $e) {
           $this->error = $e->getMessage();
           $this->response->internalError($this->error);
        }
        return false;
    }

    private function isFunctionExists(): bool
    {
        if (is_object($this->service)) {
            if (method_exists($this->service, $this->method)) {
                $this->isMethodExists = true;
                return true;
            } 
        }
        $this->response->notFound("service $this->controller/$this->method not found");
        return false;
    }

}