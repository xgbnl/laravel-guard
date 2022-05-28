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

    public function expires(): void
    {
        if ($this->expire < time()) {
            throw new InvalidArgumentException('令牌已过期，请重新登录', 403);
        }
    }

    public function login(Authenticatable $user): array
    {
        $token = $this->createToken();

        $bcrypt = md5($user->getAuthIdentifier() . time() . $token);

        $this->redis->set($user->getAuthIdentifier(), $bcrypt, $this->expire);

        return [
            'bearer_token' => $token,
            'token_type'   => 'Bearer',
            'expires_in'   => $this->expire,
        ];
    }

    public function getProvider(): Provider
    {
        return $this->provider;
    }
}
