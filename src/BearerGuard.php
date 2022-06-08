<?php

declare(strict_types=1);

namespace Xgbnl\Bearer;

use Xgbnl\Bearer\Contracts\Authenticatable;
use Xgbnl\Bearer\Contracts\Provider\Provider;

class BearerGuard extends Bearer
{
    public function logout(): void
    {
        if ($this->guest()) {
            trigger(403, '请登录后继续操作');
        }

        $this->repositories->forgeCache($this->getTokenForRequest());
    }

    public function expires(): bool
    {
        return $this->repositories->tokenExpires($this->getTokenForRequest());
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
