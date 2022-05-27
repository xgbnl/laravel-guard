<?php

declare(strict_types=1);

use Xgbnl\Bearer\Contracts\FactoryContract;
use Xgbnl\Bearer\Contracts\GuardContact;

if (!function_exists('guard')) {
    /**
     * Return new a role guard instance.
     * @param string|null $role
     * @return FactoryContract|GuardContact
     */
    function guard(string $role = null): FactoryContract|GuardContact
    {
        if (is_null($role)) {
            return app(FactoryContract::class);
        }

        return app(FactoryContract::class)->guard($role);
    }
}
