<?php

namespace Xgbnl\Bearer\Traits;

use Exception;
use Redis;

trait RedisHelpers
{
    protected ?Redis $redis = null;

    protected ?int $ttl = null;

    protected ?string $token = null;

    protected function forgeToken(): void
    {
        $this->redis->del($this->id());
    }

    /**
     * @throws Exception
     */
    protected function createToken(int $length = 64): string
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
}
