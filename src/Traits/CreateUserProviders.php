<?php

namespace Xgbnl\Bearer\Traits;

use Xgbnl\Bearer\Contracts\Provider\Provider;
use Xgbnl\Bearer\Services\UserProvider;

trait CreateUserProviders
{
    protected function createUserProvider(string $provider): Provider
    {
        if (empty($modelClass = $this->app['config']["bearer.providers.{$provider}"])) {
            trigger(
                500,
                'Could not complete instantiation of provider ,Please check your configure file bearer.php'
            );
        }

        return new UserProvider($modelClass, $provider);
    }
}
