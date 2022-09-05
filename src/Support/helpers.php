<?php

declare(strict_types=1);

use Xgbnl\Guard\Contracts\Factory;
use Xgbnl\Guard\Contracts\Guard\GuardValidator;

if (!function_exists('guard')) {
    /**
     * 返回一个守卫器实例.
     * @param string|null $role
     * @param string|array|null $relations
     * @return GuardValidator
     */
    function guard(string $role = null, string|array|null $relations = null): GuardValidator
    {
        return is_null($role)
            ? app(Factory::class)->guard()
            : app(Factory::class)->guard($role, $relations);
    }
}