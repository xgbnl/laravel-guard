<?php

declare(strict_types=1);

namespace Xgbnl\Bearer\Facades;

use Illuminate\Support\Facades\Facade;

class Bearer extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'bearer';
    }

}
