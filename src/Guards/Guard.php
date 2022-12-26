<?php

declare(strict_types=1);

namespace Xgbnl\Guard\Guards;

use http\Exception\RuntimeException;
use Xgbnl\Guard\Contracts\Authenticatable;
use Xgbnl\Guard\Contracts\Guards\StatefulGuard;
use Xgbnl\Guard\Contracts\Guards\ValidatorGuard;
use Xgbnl\Guard\Traits\GuardTrait;
use Illuminate\Database\Eloquent\Model;

class Guard extends BaseGuard implements StatefulGuard, ValidatorGuard
{
    use GuardTrait;

    public function logout(): void
    {
        if ($this->guest()) {
            throw new RuntimeException('请登录后操作', 401);
        }

        $this->token->forget();
    }

    public function login(Authenticatable|Model $user): array
    {
        return $this->token->dispatch($user);
    }
}
