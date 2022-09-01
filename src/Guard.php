<?php

declare(strict_types=1);

namespace Xgbnl\Guard;

use http\Exception\RuntimeException;
use Xgbnl\Guard\Contracts\Authenticatable;
use Xgbnl\Guard\Contracts\Provider\Provider;

class Guard extends BaseGuard
{
    public function logout(): void
    {
        if ($this->guest()) {
            throw new RuntimeException('请登录后操作',401);
        }

        $this->repositories->forgeCache($this->getTokenForRequest());
    }

    public function login(Authenticatable $user): array
    {
        return $this->repositories->store($user);
    }

    public function getProvider(): Provider
    {
        return $this->provider;
    }
}
