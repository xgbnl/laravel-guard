<?php

namespace Xgbnl\Bearer\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable;

interface Guard
{
    /**
     * 返回一个用户实例
     * @return Authenticatable|Model|null
     */
    public function user(): Authenticatable|Model|null;

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
     * 验证用户的凭据
     *
     * Validate a user's credentials.
     *
     * @param array $credentials
     * @return bool
     */
    public function validate(array $credentials = []): bool;

    /**
     * 确定守卫是否有用户实例
     *
     * Determine if the guard has a user instance.
     *
     * @return bool
     */
    public function hasUser(): bool;
}
