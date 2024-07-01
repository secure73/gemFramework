<?php

namespace Gemvc\Core;

use Gemvc\Http\Request;
use Gemvc\Http\JsonResponse;
use Gemvc\Http\Response;

class Aggregator
{
    private Request $request;
    private string $requested_service;
    private string $requested_method;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->setRequestedService();
        $this->runApp();
    }

    private function runApp(): void
    {
        if (!file_exists('./app/service/'.$this->requested_service.'.php')) {
            $this->showNotFound("the service path for so called $this->requested_service does not exists , check your service name if properly typed");
            die;
        }
        $serviceInstance = false;
        try {
            $service = 'App\\Service\\' . $this->requested_service;
            $serviceInstance = new $service($this->request);
        } catch (\Throwable $e) {
            $this->showNotFound($e->getMessage());
            die;
        }
        if (!method_exists($serviceInstance, $this->requested_method)) {
            $this->showNotFound("requested method  $this->requested_method does not exist in service, check if you type it correctly");
            die;
        }
        $method = $this->requested_method;
        $response = $serviceInstance->$method();
        if(!$response instanceof JsonResponse)
        {
            Response::internalError("method $method dose not provide JsonResponse as return value")->show();
            die;
        }
        $response->show();  
        die;
    }


    private function setRequestedService(): void
    {
        $method = "index";

        $segments = explode('/', $this->request->requestedUrl);
        $service = $segments[$_ENV["SERVICE_IN_URL_SECTION"]] ? ucfirst($segments[$_ENV["SERVICE_IN_URL_SECTION"]]) : "Index";
        if (isset($segments[$_ENV["METHOD_IN_URL_SECTION"]]) && $segments[$_ENV["METHOD_IN_URL_SECTION"]]) {
            $method = $segments[$_ENV["METHOD_IN_URL_SECTION"]];
        }
        $this->requested_service = $service;
        $this->requested_method = $method;
    }

    private function showNotFound(string $message): void
    {
        $jsonResponse = new JsonResponse();
        $jsonResponse->notFound($message);
        $jsonResponse->show();
    }
}
