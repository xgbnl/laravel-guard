<?php

declare(strict_types=1);

namespace Xgbnl\Guard;

use Illuminate\Support\Facades\Facade;

class GuardFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'guard';
    }
}
