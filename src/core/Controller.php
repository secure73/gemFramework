<?php

namespace Gemvc\Core;

use Gemvc\Http\Request;
use Gemvc\Http\Response;

/**
 * @protected  GemLibrary\Http\Request $request
 * @protected  null|string  $error
 * @function   validatePosts(array $post_schema):bool
 */
class Controller
{
    protected Request $request;
    protected ?string $error;


    public function __construct(Request $request)
    {
        $this->error = null;
        $this->request = $request;
    }

    /**
     * @param Table $object The object to map the POST data to
     * @info: automatically use $this->request->post to map to Model instance
     */
    public function mapPost(Table $object): void
    {
        $name = get_class($object);
        if (!is_array($this->request->post) || !count($this->request->post)) {
            $this->error = 'there is no incoming post detected';
            Response::badRequest("there is no incoming post detected for mappping to $name")->show();
            die();
        }
        foreach ($this->request->post as $postName => $value) {
            try {
                if (property_exists($object, $postName)) {
                    $object->$postName = $value;
                }
            } catch (\Exception $e) {
                $this->error = "post $postName cannot be set because " . $e->getMessage();
                Response::unprocessableEntity("post $postName cannot be set to $name because " . $e->getMessage())->show();
                die();
            }
        }
    }

    /**
     * @param array<string> $postNames  array of incoming post for mapping to Table Object
     * @param Table $object The object to map the POST data to
     * @set $this->error type of ?string in case of exception
     * @info: automatically use $this->request->post map to  Model instance
     */
    public function mapPostManuel(array $postNames , Table $object): void
    {
        $objectClassName = get_class($object);
        foreach($postNames as $name)
        {
            if(!isset($this->request->post[$name]))
            {
                $this->error = "there is no post found in incoming request with given name $name";
                Response::badRequest("post $name not setted on incoming request to set on $objectClassName")->show();
                die();
            }
            try {
                if (property_exists($object, $name)) {
                    $object->$name =$this->request->post[$name];
                }
                else
                {
                    $this->error = "object $objectClassName has no such property with name $name";
                    Response::unprocessableEntity("object $objectClassName has no such property with name $name")->show();
                    die();
                }
            } catch (\Exception $e) {
                $this->error = "post $name cannot be set to $objectClassName because " . $e->getMessage();
                Response::unprocessableEntity("post $name cannot be set to $objectClassName because " . $e->getMessage())->show();
                die();
            }
        }
    }

}
