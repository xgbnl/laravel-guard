<?php

namespace Xgbnl\Guard\Traits;

use Xgbnl\Guard\Contracts\Authenticatable;
use Xgbnl\Guard\Exception\GuardException;

trait GuardHelpers
{
    public function authenticate(): Authenticatable
    {
        if (!$this->check()) {
            trigger(403, '没有经过身份验证');
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

    public function id(): mixed
    {
        return $this->user()?->getModelIdentifier();
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
        $user = $this->repositories->fetchUser($this->getTokenForRequest());

        return !in_array($needle, $user);
    }
}
