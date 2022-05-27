<?php

namespace Xgbnl\Bearer\Traits;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;

trait GuardHelpers
{
    public function validate(array $credentials = []): bool
    {
        if (empty($credentials[$this->inputKey])) {
            return false;
        }

        $credentials = [$this->storageKey => $credentials[$this->inputKey]];

        if ($this->provider->retrieveByCredentials($credentials)) {
            return true;
        }

        return false;
    }

    /**
     * @throws AuthenticationException
     */
    public function authenticate(): Model|AuthenticatableContract|null
    {
        if (!is_null($user = $this->user())) {
            return $user;
        }

        throw new AuthenticationException('没有经过身份验证');
    }

    public function hasUser(): bool
    {
        return !is_null($this->user);
    }

    public function check(): bool
    {
        return !is_null($this->user());
    }

    public function guest(): bool
    {
        return !$this->check();
    }

    public function id(): mixed
    {
        return $this->user()?->getAuthIdentifier();
    }
}
