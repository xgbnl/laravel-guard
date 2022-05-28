<?php

declare(strict_types=1);

namespace Xgbnl\Bearer\Contracts\Guard;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Xgbnl\Bearer\Contracts\Provider\Provider;

interface GuardContact extends StatefulGuard
{
    /**
     * 返回提供者实例
     * @return Provider
     */
    public function getProvider(): Provider;

}
