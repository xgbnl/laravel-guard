<?php

declare(strict_types=1);

namespace Xgbnl\Bearer;

use http\Exception\InvalidArgumentException;
use Illuminate\Database\Eloquent\Model;
use JetBrains\PhpStorm\ArrayShape;
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
        return !$this->redis->exists($this->tokenKey($this->getTokenForRequest()));
    }

    #[ArrayShape(['bearer_token' => "string", 'token_type' => "string"])]
    public function login(Model|Authenticatable $user): array
    {
        $token = $this->createToken();

        $bcrypt = $this->bcrypt($token);

        $tokenKey = $this->tokenKey($token);

        $this->redis->set($tokenKey, json_encode(
                ['token' => $bcrypt, 'id' => $user->getAuthIdentifier()],
                JSON_UNESCAPED_UNICODE)
        );

        $this->redis->expire($tokenKey, 30 * 60 * 2);

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
