<?php

namespace Xgbnl\Guard\Contracts\Guard;

use Xgbnl\Guard\Contracts\Authenticatable;

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
     * 将用户从应用程序中注销
     *
     * Log the user out of the application.
     *
     * @return void
     */
    public function logout(): void;
}
