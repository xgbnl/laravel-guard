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
        $this->redis->del($this->id());
    }
}
