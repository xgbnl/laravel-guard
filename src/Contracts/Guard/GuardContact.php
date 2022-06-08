<?php

declare(strict_types=1);

namespace Xgbnl\Bearer\Contracts\Guard;

use Xgbnl\Bearer\Contracts\Provider\Provider;

interface GuardContact extends StatefulGuard
{
    /**
     * 返回提供者实例
     * @return Provider
     */
    public function getProvider(): Provider;

    /**
     * Verify that the current user ip and the ip of the cache record are the same
     * @return bool
     */
    public function validateClientIP(): bool;

    /**
     * Verify that the current user device and the device of the cache record are the same
     * @return bool
     */
    public function validateDevice(): bool;
}
