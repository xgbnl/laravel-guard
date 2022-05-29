<?php

namespace Xgbnl\Bearer\Contracts\Guard;

use Xgbnl\Bearer\Contracts\Authenticatable;

interface Guard
{
    /**
     * 返回一个用户实例
     * @return Authenticatable|null
     */
    public function user(): ?Authenticatable;

    /**
     * 确定当前用户已经过身份验证
     *
     * Determine if the current user is authenticated.
     *
     * @return bool
     */
    public function check(): bool;

    /**
     * 确定当前用户是否是访客
     *
     * Determine if the current user is a guest.
     *
     * @return bool
     */
    public function guest(): bool;

    /**
     * 获取当前经过身份验证的用户的 ID
     *
     * Get the ID for the currently authenticated user.
     *
     * @return mixed
     */
    public function id(): mixed;

    /**
     * 确定守卫是否有用户实例
     *
     * Determine if the guard has a user instance.
     *
     * @return bool
     */
    public function hasUser(): bool;

    /**
     * 认证用户
     * @return Authenticatable
     */
    public function authenticate(): Authenticatable;
}
