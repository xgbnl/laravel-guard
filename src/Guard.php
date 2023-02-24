<?php

declare(strict_types=1);

namespace Xgbnl\Guard;

use Illuminate\Support\Facades\Facade;
use Illuminate\Database\Eloquent\Model;
use Xgbnl\Guard\Contracts\Authenticatable;
use Xgbnl\Guard\Contracts\Factory;
use Xgbnl\Guard\Contracts\Guards\GuardContact;
use Xgbnl\Guard\Contracts\Guards\StatefulGuard;
use Xgbnl\Guard\Contracts\Guards\ValidatorGuard;

/**
 * @method GuardContact|StatefulGuard|ValidatorGuard guard(string $role = null)
 * @method array login(Authenticatable|Model $user)
 * @method void logout()
 * @method bool check()
 * @method bool guest()
 * @method bool hasUser()
 * @method Authenticatable authenticate()
 */
class Guard extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return Factory::class;
    }
}
