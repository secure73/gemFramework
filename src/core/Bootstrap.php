<?php

namespace GemFramework\Core;

use GemLibrary\Http\JsonResponse;
use GemLibrary\Http\GemRequest;


class Bootstrap
{
    //public ?object      $service;
    public string        $service;
    public string        $method;
    private GemRequest   $gemRequest;
    private ?string      $error;
    private ?object      $instance;

    public function __construct(GemRequest $gemRequest)
    {
        $this->error = null;
        $this->service = 'Index';
        $this->method = 'index';
        $this->gemRequest = $gemRequest; 
        $this->runApp()->show();
    }

    public function runApp():JsonResponse
    {
        $jsonResponse = new JsonResponse();
        $this->setService();
        if(!$this->serviceFileExists())
        {
            return $jsonResponse->notFound(" requested service $this->service not found");	
        }
        if (!$this->createServiceInstance()) {       
           return $jsonResponse->internalError($this->error);
        }
        if(!method_exists($this->instance,$this->method))
        {
            return $jsonResponse->notFound("requested method $this->method not found");
        }
        $method = $this->method;
        $response = $this->instance->$method();
        if(!is_a($response , 'JsonResponse'))
        {
            return $jsonResponse->internalError("method $this->method dose not provide JsonResponse as return value");
        }
        $jsonResponse = null;
        return $response;
    }


    private function serviceFileExists():bool
    {
        return file_exists('../../../app/service/'.$this->service.'.php');
    }

    private function createServiceInstance():bool
    {
        $service = 'App\\Controller\\'.$this->service;
        $instance = false;
        try{
          $instance = new $service($this->gemRequest);
          $this->instance = $instance;
          return true;
        }
        catch(\Exception $e)
        {
            $this->error = $e->getMessage();
        }
        return false; 
    }

    private function setService():void
    {
        $segments = explode('/',$this->gemRequest->requestedUrl);
        if(isset($segments[$_ENV['URI_SERVICE_SEGMENT']]) && $segments[$_ENV['URI_SERVICE_SEGMENT']] !== "")
        {
            $this->service = ucfirst(trim($segments[$_ENV['URI_SERVICE_SEGMENT']]));
            if(isset($segments[$_ENV['URI_METHOD_SEGMENT']]) && $segments[$_ENV['URI_METHOD_SEGMENT']] !== "")
            {
                $this->method = $segments[$_ENV['URI_METHOD_SEGMENT']];
            }
        }
    }

}
