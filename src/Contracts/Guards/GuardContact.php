<?php

declare(strict_types=1);

namespace Xgbnl\Guard\Contracts\Guards;

use Xgbnl\Guard\Contracts\Authenticatable;

interface GuardContact
{
    /**
     * 返回认证后的用户实例
     * @param string|array|null $relations
     * @return Authenticatable|null
     */
    public function user(string|array|null $relations): ?Authenticatable;
}
