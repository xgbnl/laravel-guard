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
            throw new BearerException('请登录后再试', 403);
        }

        $this->forgeToken();
    }

    public function expires(): bool
    {
        return $this->expiresIn();
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
