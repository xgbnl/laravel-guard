<?php

declare(strict_types=1);

namespace Xgbnl\Bearer;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class BearerGuard extends Bearer
{
    public function logout(): void
    {

    }


    public function expires(): void
    {

    }

    public function login(Model|Authenticatable $user): array
    {

    }
}
