<?php

declare(strict_types=1);

use Xgbnl\Guard\Contracts\Factory\Factory;
use Xgbnl\Guard\Contracts\Guard\GuardContact;

if (!function_exists('guard')) {
    /**
     * Return new a role guard instance.
     * @param string|null $role
     * @param string|array|null $relations
     * @return GuardContact
     */
    function guard(string $role = null, string|array|null $relations = null): GuardContact
    {
        return is_null($role)
            ? app(Factory::class)->guard()
            : app(Factory::class)->guard($role, $relations);
    }
}