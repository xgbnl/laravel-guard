<?php

declare(strict_types=1);

namespace Xgbnl\Bearer;

use http\Exception\InvalidArgumentException;
use Xgbnl\Bearer\Contracts\Authenticatable;
use Xgbnl\Bearer\Contracts\Provider\Provider;

class BearerGuard extends Bearer
{
    public function logout(): void
    {
        if (is_null($this->user)) {
            throw new InvalidArgumentException('用户未登录');
        }

        $this->forgeToken();
    }

    public function expires(): bool
    {
        return $this->expire < time();
    }

    public function login(Authenticatable $user): array
    {
        $token = $this->createToken();

        $bcrypt = $this->bcrypt($token);

        $this->redis->set($bcrypt,$user->getAuthIdentifier());

        return [
            'bearer_token' => $token,
            'token_type'   => 'Bearer',
        ];
    }

    public function getProvider(): Provider
    {
        return $this->provider;
    }
}
