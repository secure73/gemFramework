<?php

namespace GemFramework\Core;

use GemLibrary\Http\JsonResponse;
use GemLibrary\Http\GemRequest;


class Bootstrap
{
    public GemRequest   $gemRequest;
    public ?object      $service;
    public string       $controller;
    public string       $method;
    public ?string      $error;

    public function __construct(GemRequest $gemRequest)
    {
        $this->controller = 'Index';
        $this->method = 'index';
        $this->gemRequest = $gemRequest;
        $segments = explode('/',$this->gemRequest->requestedUrl);
        if(isset($segments[URI_CONTROLLER_SEGMENT]) && $segments[URI_CONTROLLER_SEGMENT] !== "")
        {
            $this->controller = 'App\\Controller\\'.ucfirst(trim($segments[URI_CONTROLLER_SEGMENT])).'Controller';
        }
        if(isset($segments[URI_METHOD_SEGMENT]) && $segments[URI_METHOD_SEGMENT] !== "")
        {
            $this->method = $segments[URI_METHOD_SEGMENT];
        }
        $this->runApp();
    }

    public function runApp():void
    {
        if (!$this->makeInstanceService()) {     
            $jsonResponse = new JsonResponse();
            $jsonResponse->internalError($this->error);
            $jsonResponse->show();
        }
        
    }

    private function makeInstanceService(): bool
    {
        try {
            $controller = $this->controller;
            $method = $this->method;
            $ins = new $controller($this->gemRequest);
            $res = $ins->$method();
            if (!$res instanceof JsonResponse) {
                $this->error = 'Method ' . $this->controller . '/' . $this->method . 'dosent return Object of JsonResponse';
            } else {
                $res->show();
                die;
            }
            return true;
        } catch (\Throwable $e) {
            $this->error = $e->getMessage();
        }
        return false;
    }
}