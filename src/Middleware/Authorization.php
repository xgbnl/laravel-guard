<?php

declare(strict_types=1);

namespace Xgbnl\Guard\Middleware;

use Illuminate\Http\Request;
use Xgbnl\Guard\Contracts\Factory;
use Xgbnl\Guard\Contracts\Guard\ValidatorGuard;
use Xgbnl\Guard\Contracts\Guard\GuardContact;
use Xgbnl\Guard\Contracts\Guard\StatefulGuard;

abstract class Authorization
{
    private readonly Factory                                        $factory;
    private GuardContact|ValidatorGuard|StatefulGuard|null $guard = null;

    final public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    public function handle(Request $request, \Closure $next, string $role)
    {
        $this->guard = $this->factory->guard($role);

        $this->doHandle();

        return $next($request);
    }

    /**
     * 抽象处理方法.
     * @return void
     */
    abstract public function doHandle(): void;

    /**
     * 验证客户端IP.
     * @return bool
     */
    final protected function validateClientIP(): bool
    {

    }

    /**
     * 验证设备.
     * @return bool
     */
    final protected function validateDevice(): bool
    {

    }
    
    final protected function guard(): GuardContact|ValidatorGuard|StatefulGuard
    {
        return $this->guard;
    }
}
