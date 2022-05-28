<?php

declare(strict_types=1);

namespace Xgbnl\Bearer\Middleware;

use Illuminate\Http\Request;
use Closure;

class BearerAuthorization extends Authorization
{
    public function handle(Request $request, Closure $next, string $role)
    {
        $this->guard($role)->expires()->guest();

        return $next($request);
    }
}
