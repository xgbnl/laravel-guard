<?php

declare(strict_types=1);

namespace Xgbnl\Bearer\Middleware;

use Illuminate\Http\Request;
use Xgbnl\Bearer\Contracts\FactoryContract;
use Closure;

abstract class Authorization
{
    protected FactoryContract $auth;

    final public function __construct(FactoryContract $auth)
    {
        $this->auth = $auth;
    }

    abstract public function handle(Request $request,\Closure $closure,...$guards);

}
