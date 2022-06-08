<?php

declare(strict_types=1);

namespace Xgbnl\Bearer\Middleware;

class BearerAuthorization extends Authorization
{
    public function doHandle()
    {

        if ($this->guard()->guest()) {
            trigger(403,'请登录后重试');
        }

        if ($this->guard()->expires()) {
           trigger(403,'令牌已失效,请重新登录');
        }

        if ($this->guard()->validateDevice()){
            // 当前访问IP和上次访问IP不一致时,进行验证

        }
    }
}
