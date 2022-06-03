<?php

declare(strict_types=1);

namespace Xgbnl\Bearer;

use Exception;
use Illuminate\Database\Eloquent\Model;
use JetBrains\PhpStorm\ArrayShape;
use Xgbnl\Bearer\Contracts\Authenticatable;
use Xgbnl\Bearer\Contracts\Provider\Provider;
use Xgbnl\Bearer\Exception\BearerException;

class BearerGuard extends Bearer
{
    /**
     * @throws BearerException
     */
    public function logout(): void
    {
        if (is_null($this->user)) {
            throw new BearerException('未登录无法使用注销功能', 403);
        }

        $this->forgeToken();
    }

    public function expires(): bool
    {
        return $this->redis->ttl($this->additionTokenHeader($this->getTokenForRequest())) <= 360;
    }

    /**
     * @throws Exception
     */
    #[ArrayShape(['access_token' => "string", 'type' => "string"])]
    public function login(Model|Authenticatable $user): array
    {
        return $this->store($user);
    }

    public function getProvider(): Provider
    {
        return $this->provider;
    }
}
