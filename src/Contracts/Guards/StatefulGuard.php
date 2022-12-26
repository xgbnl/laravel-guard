<?php

namespace Xgbnl\Guard\Contracts\Guards;

use Xgbnl\Guard\Contracts\Authenticatable;
use Illuminate\Database\Eloquent\Model;

interface StatefulGuard
{
    /**
     * 将用户登录至应用程序
     * @param Model|Authenticatable $user
     * @return array
     */
    public function login(Model|Authenticatable $user): array;

    /**
     * 将用户从应用程序中注销
     * @return void
     */
    public function logout(): void;
}
