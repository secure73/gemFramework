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
    public bool         $isMethodExists;
    public ?string      $error;

    public function __construct(GemRequest $gemRequest)
    {
        $this->gemRequest = $gemRequest;

        $segments = explode($gemRequest->requestedUrl, '/');
        isset($segments[1]) ? $this->controller = ucfirst($segments[1]) : $this->controller = 'Index';
        isset($segments[2]) ? $this->method = $segments[2] : $this->method = 'index';

        $this->isMethodExists = false;
    }

    public function runApp(): JsonResponse
    {
        if ($this->makeInstanceService()) {
            if ($this->isFunctionExists()) {
                $res = $this->service->method();
                if (!$res instanceof JsonResponse) {
                    $jsonResponse = new JsonResponse();
                    $jsonResponse->internalError('Method ' . $this->controller . '/' . $this->method . 'dosent return Object of JsonResponse');
                    $jsonResponse->show();
                    die;
                } else {
                    $res->show();
                }
            }
        }
        $jsonResponse = new JsonResponse();
        $jsonResponse->notFound($this->error);
        die;
    }

    private function makeInstanceService(): bool
    {
        try {
            $service = 'App\\Controller\\' . $this->controller . 'Controller';
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
                $this->isMethodExists = true;
                return true;
            }
        }
        $this->error = "service $this->controller/$this->method not found";
        return false;
    }
}
