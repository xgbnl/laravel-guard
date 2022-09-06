<?php

namespace Xgbnl\Guard\Contracts\Guards;

use Xgbnl\Guard\Contracts\Authenticatable;

interface StatefulGuard extends Guard
{
    /**
     * 将用户登录至应用程序
     * @param Authenticatable $user
     * @return array
     */
    public function login(Authenticatable $user): array;

    /**
     * 将用户从应用程序中注销
     * @return void
     */
    public function logout(): void;
}
