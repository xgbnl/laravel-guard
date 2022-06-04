<?php

declare(strict_types=1);

namespace Xgbnl\Bearer;

use Exception;
use Illuminate\Database\Eloquent\Model;
use JetBrains\PhpStorm\ArrayShape;
use Xgbnl\Bearer\Contracts\Authenticatable;
use Xgbnl\Bearer\Contracts\Provider\Provider;

class BearerGuard extends Bearer
{
    public function logout(): void
    {
        if (is_null($this->user)) {
            trigger(403, '请登录后再尝试使用注销功能');
        }

        $this->repositories->clearToken($this->getTokenForRequest());
    }

    public function expires(): bool
    {
        return $this->repositories->tokenExpires($this->getTokenForRequest());
    }

    /**
     * @throws Exception
     */
    #[ArrayShape(['access_token' => "string", 'type' => "string"])]
    public function login(Model|Authenticatable $user): array
    {
        return $this->repositories->store($user);
    }

    public function getProvider(): Provider
    {
        return $this->provider;
    }
}
