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
        if(isset($segments[2]) && $segments[2] !== "")
        {
            $this->controller = ucfirst($segments[2]);
        }
        if(isset($segments[3]) && $segments[3] !== "")
        {
            $this->method = $segments[3];
        }
        $this->runApp();
    }

    public function runApp():void
    {
        if ($this->makeInstanceService()) {
            if ($this->isFunctionExists()) {
                $method = $this->method;
                $res = $this->service->$method();
                if (!$res instanceof JsonResponse) {
                    $jsonResponse = new JsonResponse();
                    $jsonResponse->internalError('Method ' . $this->controller . '/' . $this->method . 'dosent return Object of JsonResponse');
                    $jsonResponse->show();
                    die;
                } else {
                    $res->show();
                    die;
                }
            }
        }
        $jsonResponse = new JsonResponse();
        $jsonResponse->notFound($this->error);
        $jsonResponse->show();
        
    }

    private function makeInstanceService(): bool
    {

        try {
            $service =  'App\\Controller\\' . $this->controller . 'Controller';
            $this->service = new $service($this->gemRequest);
            return true;
        } catch (\Throwable $e) {
            $this->error = $e->getMessage();
        }
        return false;
    }

    private function isFunctionExists(): bool
    {
        if (is_object($this->service)) {
            if (method_exists($this->service, $this->method)) {
                return true;
            }
        }
        $this->error = "service $this->controller/$this->method not found";
        return false;
    }
}