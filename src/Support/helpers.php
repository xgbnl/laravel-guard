<?php

declare(strict_types=1);

use Xgbnl\Guard\Contracts\Factory;
use Xgbnl\Guard\Contracts\Guard\ValidatorGuard;
use Xgbnl\Guard\Contracts\Guard\GuardContact;
use Xgbnl\Guard\Contracts\Guard\StatefulGuard;

if (!function_exists('guard')) {
    /**
     * 返回一个守卫器实例
     * @param string|null $role
     * @return ValidatorGuard|GuardContact|StatefulGuard
     */
    function guard(string $role = null): ValidatorGuard|GuardContact|StatefulGuard
    {
        return is_null($role) ? app(Factory::class)->guard() : app(Factory::class)->guard($role);
    }
}