<?php
namespace GemFramework\Core;
use GemLibrary\Http\GemRequest;
use GemLibrary\Http\JsonResponse;

class Controller {
    protected JsonResponse $response;
    protected GemRequest $request;
    
    public $model;/** @phpstan-ignore-line */

    public function __construct(GemRequest $request) {
        $this->request = $request;
        $this->response = new JsonResponse();
    }

    /**
     * @param array<string> $postSchema
     */
    public function checkPosts(array $postSchema):bool
    {
        if(!$this->request->definePostSchema($postSchema))
        {
                $this->response->badRequest($this->request->error);
                return false;
        }
        return true;
    }
}
?>
