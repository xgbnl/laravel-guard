<?php

declare(strict_types=1);

namespace Xgbnl\Guard\Middleware;

use Xgbnl\Guard\Exception\GuardException;

class GuardMiddleware extends Authorization
{
    public function doHandle()
    {
        if ($this->guard()->guest()) {
            throw new GuardException('无效令牌访问,请登录', 401);
        }
    }
}
