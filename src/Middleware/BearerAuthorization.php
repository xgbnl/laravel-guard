<?php

declare(strict_types=1);

namespace Xgbnl\Bearer\Middleware;

class BearerAuthorization extends Authorization
{
    public function doHandle()
    {
        if ($this->guard()->guest()) {
            trigger(401, '无效令牌访问,请登录');
        }

        if ($this->guard()->expires()) {
            trigger(401, '令牌已过期,请重新登录');
        }
    }
}
