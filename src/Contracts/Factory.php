<?php

namespace Xgbnl\Guard\Contracts;

use Xgbnl\Guard\Contracts\Guards\GuardContact;
use Xgbnl\Guard\Contracts\Guards\StatefulGuard;
use Xgbnl\Guard\Contracts\Guards\ValidatorGuard;

interface Factory
{
    /**
     * 返回一个守卫器实例
     *
     * @param string $role
     */
    public function guard(string $role): GuardContact|StatefulGuard|ValidatorGuard;

    /**
     * 定义应该使用的默认守卫
     *
     * @param string $name
     * @return void
     */
    public function shouldUse(string $name): void;
}
