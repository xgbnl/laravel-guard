<?php

namespace Xgbnl\Bearer\Contracts\Guard;

use Xgbnl\Bearer\Contracts\Authenticatable;

interface StatefulGuard extends Guard
{
    /**
     * 将用户登录至应用程序
     *
     * Log a user into the application.
     *
     * @param Authenticatable $user
     * @return array
     */
    public function login(Authenticatable $user): array;

    /**
     * 确定 token 是否过期
     *
     * Determine if token expires in.
     * @return bool
     */
    public function expires(): bool;

    /**
     * 将用户从应用程序中注销
     *
     * Log the user out of the application.
     *
     * @return void
     */
    public function logout(): void;
}
