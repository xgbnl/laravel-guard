<?php

namespace Xgbnl\Guard\Traits;

use http\Exception\RuntimeException;
use Xgbnl\Guard\Contracts\Authenticatable;

trait GuardTrait
{
    public function authenticate(): Authenticatable
    {
        if (!$this->check()) {
            throw new RuntimeException('无效令牌,无法验证身份,请登录', 401);
        }

        return $this->user();
    }

    public function hasUser(): bool
    {
        return !is_null($this->user());
    }

    public function check(): bool
    {
        return !is_null($this->user());
    }

    public function guest(): bool
    {
        return !$this->check();
    }
}
