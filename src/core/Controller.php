<?php

namespace Gemvc\Core;

use Gemvc\Http\Request;

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
     * @return bool
     * validatePosts(['email'=>'email' , 'id'=>'int' , '?name' => 'string'])
     * @help : ?name means it is optional
     */
    protected function validatePosts(array $post_schema): bool
    {
        if (!$this->request->definePostSchema($post_schema)) {
            $this->error = $this->request->error;
            return false;
        }
        return true;
    }

    /**
     * Validates string lengths in a dictionary against min and max constraints.
     *
     * @param array<string> $post_schema A dictionary where keys are strings and values are strings in the format "key:min-value|max-value" (optional).
     * @return bool True if all strings pass validation, False otherwise.
     */
    protected function validateStringPosts(array $post_schema): bool
    {
        if (!$this->request->validateStringPosts($post_schema)) {
            $this->error = $this->request->error;
            return false;
        }
        return true;
    }
}
