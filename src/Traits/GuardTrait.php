<?php

namespace Xgbnl\Guard\Traits;

use http\Exception\RuntimeException;
use Xgbnl\Guard\Contracts\Authenticatable;

trait GuardTrait
{
    public function authenticate(): Authenticatable
    {
        if (!$this->check()) {
            throw new RuntimeException('没有经过身份验证,请登录后再试', 401);
        }

        return $this->user();
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

    public function validateClientIP(): bool
    {
        return $this->validate($this->request->getClientIp());
    }

    public function validateDevice(): bool
    {
        return $this->validate($this->request->userAgent());
    }

    private function validate(string $needle): bool
    {
        $user = $this->token->fetchUser($this->getTokenForRequest());

        return !in_array($needle, $user);
    }
}
