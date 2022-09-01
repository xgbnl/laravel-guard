<?php

namespace Xgbnl\Guard\Traits;

use Xgbnl\Guard\Contracts\Provider\Provider;
use Xgbnl\Guard\Services\UserProvider;

trait CreateUserProviders
{
    protected function createUserProvider(string $provider,string|array|null $relations): Provider
    {
        if (empty($modelClass = $this->app['config']["bearer.providers.{$provider}"])) {
            trigger(
                500,
                'Could not complete instantiation of provider ,Please check your configure file guard.php'
            );
        }

        return new UserProvider($modelClass, $provider,$relations);
    }
}
