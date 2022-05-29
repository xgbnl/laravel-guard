<?php

namespace Xgbnl\Bearer\Traits;

use Xgbnl\Bearer\Contracts\Authenticatable;
use Xgbnl\Bearer\Exception\BearerException;

trait GuardHelpers
{
    /**
     * @throws BearerException
     */
    public function authenticate(): Authenticatable
    {
        if (!$this->check()) {
            throw new BearerException('没有经过身份验证');
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
        return $this->user()?->getAuthIdentifier();
    }
}
