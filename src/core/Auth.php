<?php

namespace Gemvc\Core;

use Gemvc\Http\JWTToken;
use Gemvc\Http\Request;
use Gemvc\Http\Response;

/**
 * @function success()
 * @function authorize(array<sting> $roles)
 */
class Auth
{
    private Request $request;
    public ?JWTToken $token;
    private bool $isAuthenticated;
    private ?int $user_id;
    /**
     * Summary of user_roles
     * @var array<string> $user_roles
     */
    private ?array $user_roles;
    public ?string $error;
    /**
     * Summary of __construct
     * @param \Gemvc\Http\Request $request
     * @param array<string>|null $arrayRolesToAuthorize
     */
    public function __construct(Request $request, array $arrayRolesToAuthorize = null)
    {
        $this->request = $request;
        $this->isAuthenticated = false;
        $this->user_id = null;
        $this->user_roles = null;
        $this->token = null;
        $this->error = null;

        if (!$this->authenticate()) {
            Response::forbidden($this->error ?? 'Authentication failed')->show();
            die;
        }

        if ($arrayRolesToAuthorize){
            $this->authorize($arrayRolesToAuthorize);
        }
        $this->isAuthenticated = true;
    }

    /**
     * determine if request is successfully Authenticated
     * @return bool
     */
    public function success(): bool
    {
        return $this->isAuthenticated;
    }

    public function getUserId(): int|null
    {
        return $this->user_id;
    }

    /**
     * Summary of getUserRoles
     * @return array<string>|null
     */
    public function getUserRoles(): array|null
    {
        return $this->user_roles;
    }

     /**
     * @param array<string> $roles
     * @return bool
     */
    public function authorize(array $roles): bool
    {
        if(!$this->token )
        {
            Response::forbidden("contain no token ")->show();
            die;
        }

      if($this->token->role && strlen($this->token->role) > 1){

            $user_roles = explode(',',$this->token->role);
            foreach ($roles as $role) {
                if (in_array($role, $user_roles)) {
                    return true;
                }
            }
        }
        $roleText = $this->token->role;
        Response::unauthorized("role $roleText  not allowed to perform this action")->show();
        die();
    }

    private function authenticate(): bool
    {
        $existed_token = $this->request->getJwtToken();

        if ($existed_token && $existed_token->verify()) {
            $this->token = $existed_token;
            $this->isAuthenticated = true;
            $this->user_id = $existed_token->user_id;
            $this->user_roles = $existed_token->role ? explode(',', $existed_token->role) : null;
            return true;
        }

        $jwt = new JWTToken();
        if (!$jwt->extractToken($this->request)) {
            $this->error = 'Failed to extract token';
            return false;
        }
        
        if (!$jwt->verify()) {
            $this->error = $jwt->error;
            return false;
        }

        $this->token = $jwt;
        $this->isAuthenticated = true;
        $this->request->setJwtToken($jwt);
        $this->user_id = $jwt->user_id;
        $this->user_roles = $jwt->role ? explode(',', $jwt->role) : null;
        
        return true;
    }
}
