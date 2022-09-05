<?php

declare(strict_types=1);

namespace Xgbnl\Guard\Contracts\Guard;

use Xgbnl\Guard\Contracts\Authenticatable;

interface ValidatorGuard
{
    /**
     * 确定当前用户已经过身份验证
     * @return bool
     */
    public function check(): bool;

    /**
     * 确定当前用户是否是访客
     * @return bool
     */
    public function guest(): bool;

    /**
     * 确定守卫是否有用户实例
     * @return bool
     */
    public function hasUser(): bool;

    /**
     * 获取认证用户
     * @return Authenticatable
     */
    public function authenticate(): Authenticatable;
}
