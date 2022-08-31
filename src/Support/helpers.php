<?php

declare(strict_types=1);

use Xgbnl\Bearer\Contracts\Factory\Factory as FactoryContract;
use Xgbnl\Bearer\Contracts\Guard\GuardContact;
use Xgbnl\Bearer\Exception\BearerException;

if (!function_exists('guard')) {
    /**
     * Return new a role guard instance.
     * @param string|null $role
     * @param string|array $relations
     * @return GuardContact
     */
    function guard(string $role = null, string|array $relations = []): GuardContact
    {
        return is_null($role)
            ? app(FactoryContract::class)->guard()
            : app(FactoryContract::class)->guard($role, $relations);
    }
}

if (!function_exists('trigger')) {

    /**
     * Trigger error
     * @param int $code
     * @param string $message
     * @return never
     * @throws BearerException
     */
    function trigger(int $code, string $message): never
    {
        throw new BearerException($message, $code);
    }
}
