<?php

declare(strict_types=1);

namespace Xgbnl\Bearer\Middleware;

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

    public function handle(Request $request, \Closure $next, string $role)
    {
        $this->guard = $this->auth->guard($role,null);

        $this->doHandle();

        return $next($request);
    }

    abstract public function doHandle();

    final protected function guard(): GuardContact
    {
        return $this->guard;
    }
}
