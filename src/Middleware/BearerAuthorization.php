<?php

declare(strict_types=1);

namespace Xgbnl\Bearer\Middleware;

use Xgbnl\Bearer\Exception\BearerException;

class BearerAuthorization extends Authorization
{
    /**
     * @throws BearerException
     */
    public function doHandle()
    {

        if ($this->guard()->guest()) {
            throw new BearerException('请登录', 403);
        }

        if ($this->guard()->expires()) {
            throw new BearerException('令牌已过期,请重新登录', 403);
        }

    }
}
