<?php

namespace Xgbnl\Bearer\Traits;

use Illuminate\Foundation\Application;
use Xgbnl\Bearer\Contracts\Provider\Provider;
use Xgbnl\Bearer\Services\UserProvider;

trait CreateUserProviders
{
    protected function createUserProvider(Application $app, string $provider): Provider
    {
        return new UserProvider($app, $provider);
    }
}
