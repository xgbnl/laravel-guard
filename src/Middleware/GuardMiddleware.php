<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Xgbnl\Guard\Exception\GuardException;
use Xgbnl\Guard\Middleware\Authorization;
use Closure;

class GuardMiddleware extends Authorization
{
    public function doHandle(Request $request, Closure $next, string $role)
    {
        if ($this->guard()->guest()) {
            throw new GuardException('无效令牌访问,请登录', 401);
        }

        return $next($request);
    }
}
