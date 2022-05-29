<?php

namespace Xgbnl\Bearer\Traits;

use Exception;
use Redis;

trait RedisHelpers
{
    protected ?Redis $redis = null;

    protected ?string $token = null;

    protected function configure(string $name): void
    {
        $this->redis = \Illuminate\Support\Facades\Redis::connection($name)->client();
    }

    protected function forgeToken(): void
    {
        $this->redis->del([$this->tokenKey($this->getTokenForRequest())]);
    }

    protected function tokenKey(string $token): string
    {
        return 'sys:user:token' . $token;
    }

    final protected function createToken(int $length = 64): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $alphabet .= 'abcdefghijklmnopqrstuvwxyz';
        $alphabet .= '0123456789';

        $max = strlen($alphabet);

        for ($i = 0; $i < $length; $i++) {
            $this->token .= $alphabet[random_int(0, $max - 1)];
        }

        return $this->token;
    }

    final protected function bcrypt(string $token): string
    {
        return match ($this->encryption) {
            'hash' => hash('sha256', $token),
            'md5'  => hash('md5', $token),
        };
    }
}
