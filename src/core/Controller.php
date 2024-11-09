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
     * @param array<string> $post_schema  Define Post Schema to validation
     * @return void
     * validatePosts(['email'=>'email' , 'id'=>'int' , '?name' => 'string'])
     * if not validated , automatic show response and die;
     * @help : ?name means it is optional
     */
    protected function validatePosts(array $post_schema): void
    {
        if (!$this->request->definePostSchema($post_schema)) {
            Response::badRequest($this->request->error)->show();
            die;
        }
    }

    /**
     * Validates string lengths in a dictionary against min and max constraints.
     * if not validated , automatic show response and die;
     * A dictionary where keys are strings and values are strings in the format "key:min-value|max-value" (optional).
     * @param array<string> $post_schema an Array where keys are post name and values are strings in the format "key:min-value|max-value" (optional).
     * validateStringPosts([
     *     'username' => '3|15',  // Min length 3, max length 15
     *     'password' => '8|',    // Min length 8, no max limit
     *     'nickname' => '|20',   // No min limit, max length 20
     *     'bio' => '',           // No min or max limit
     * ]);
     */
    protected function validateStringPosts(array $post_string_schema): void
    {
        if (!$this->request->validateStringPosts($post_string_schema)) {
            Response::badRequest($this->request->error)->show();
            die;
        }
    }
}
