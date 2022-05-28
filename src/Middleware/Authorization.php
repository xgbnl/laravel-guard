<?php

declare(strict_types=1);

namespace Xgbnl\Bearer\Middleware;

use http\Exception\InvalidArgumentException;
use Illuminate\Http\Request;
use Xgbnl\Bearer\Contracts\Factory\Factory;
use Xgbnl\Bearer\Contracts\Guard\GuardContact;

abstract class Authorization
{
    private Factory       $auth;
    private ?GuardContact $guard = null;

    final public function __construct(Factory $auth)
    {
        $this->auth = $auth;
    }

    abstract public function handle(Request $request, \Closure $next, string $role);

    final protected function guard(string $guard): self
    {
        $this->guard = $this->auth->guard($guard);

        return $this;
    }

    final protected function guest(): self
    {
        if ($this->guard->guest()) {
            throw new InvalidArgumentException('请登录后重试');
        }
        return $this;
    }

    final protected function expires(): self
    {
        if ($this->guard->expires()) {
            throw new InvalidArgumentException('令牌已过期,请重新登录', 403);
        }

        return $this;
    }
}
