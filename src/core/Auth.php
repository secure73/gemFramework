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
        $this->request = $request;
        $this->token = null;
        $this->error = null;
        if (!$this->authenticate()) {
            Response::forbidden($this->error)->show();
            die;
        }
        if (is_array($arrayRolesToAuthorize) && count($arrayRolesToAuthorize)) {
            if (!$this->authorize($arrayRolesToAuthorize)) {
                // @phpstan-ignore-next-line
                Response::unauthorized("role {$this?->token?->role} is not allowed to perform this action")->show();
                die;
            }
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

    public function getUserRoles(): array|null
    {
        return $this->user_roles;
    }

     /**
     * @param array<string> $roles
     * @return bool
     */
    private function authorize(array $roles): bool
    {
        // @phpstan-ignore-next-line
        $user_roles = explode(',',$this->token->role);
        foreach ($roles as $role) {
            if (in_array($role, $user_roles)) {
                return true;
            }
        }
        
        return true;
    }

    private function checkExistedProcessedRequest(): bool
    {
        if (!$this->request->getJwtToken()) {
            return false;
        }
        return true;
    }

    private function authenticate(): bool
    {

        if (!$this->checkExistedProcessedRequest()) {
            $jwt = new JWTToken();
            if (!$jwt->extractToken($this->request)) {
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
            $this->user_roles = explode(',', $jwt->role);
            return true;
        }
        $existed_token = $this->request->getJwtToken();

        if (!$existed_token || !$existed_token->verify()) {
            return false;
        }
        $this->token = $existed_token;
        $this->isAuthenticated = true;
        $this->user_id = $existed_token->user_id;
        $this->user_roles = explode(',', $existed_token->role);
        return true;
    }
}
