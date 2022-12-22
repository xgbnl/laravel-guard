<?php

declare(strict_types=1);

namespace Xgbnl\Guard\Middleware;

use Closure;
use Illuminate\Http\Request;
use Xgbnl\Guard\Contracts\Factory;
use Xgbnl\Guard\Contracts\Guards\ValidatorGuard;
use Xgbnl\Guard\Contracts\Guards\GuardContact;
use Xgbnl\Guard\Contracts\Guards\StatefulGuard;

abstract class Authorization
{
    private readonly Factory                                        $factory;
    private GuardContact|ValidatorGuard|StatefulGuard|null $guard = null;

    final public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    public function handle(Request $request, Closure $next, string $role)
    {
        $this->guard = $this->factory->guard($role);

        $this->doHandle();

        return $next($request);
    }

    /**
     * 抽象处理方法.
     * @param Request $request
     * @param Closure $next
     * @param string $role
     * @return void
     */
    abstract public function doHandle(Request $request, Closure $next, string $role): void;
    
    final protected function guard(): GuardContact|ValidatorGuard|StatefulGuard
    {
        return $this->guard;
    }
}
