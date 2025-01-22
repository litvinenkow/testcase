<?php

namespace App\Auth;

use App\Services\JwtService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;

class JwtGuard implements Guard
{
    protected $request;
    protected $provider;
    protected $user = null;
    protected $jwt;

    public function __construct(UserProvider $provider, Request $request)
    {
        $this->request = $request;
        $this->provider = $provider;
        $this->jwt = new JwtService();
    }

    public function user()
    {
        if (!is_null($this->user)) {
            return $this->user;
        }

        $user = null;

        $token = $this->jwt->getTokenFromRequest($this->request);

        if (!empty($token)) {
            try {
                if ($payload = $this->jwt->setToken($token)->getPayload()) {
                    $user = $this->provider->retrieveById($payload->sub);
                }
            } catch (JwtException $e) {
                logger()->error($e->getMessage());
            }
        }

        return $this->user = $user;
    }

    public function setUser(Authenticatable $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function check()
    {
        return !is_null($this->user());
    }

    public function guest()
    {
        return !$this->check();
    }

    public function hasUser()
    {
        return !$this->check();
    }

    public function id()
    {
        if ($this->user()) {
            return $this->user()->getAuthIdentifier();
        }
    }

    public function validate(array $credentials = [])
    {
        if (empty($credentials['email']) || empty($credentials['password'])) {
            throw new JwtException('Empty credentials on validation');
        }

        $user = $this->provider->retrieveByCredentials($credentials);

        if (!is_null($user) && $this->provider->validateCredentials($user, $credentials)) {
            $this->setUser($user);

            return true;
        } else {
            return false;
        }
    }
}
