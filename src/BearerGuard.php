<?php

declare(strict_types=1);

namespace Xgbnl\Bearer;

use Illuminate\Database\Eloquent\Model;
use JetBrains\PhpStorm\ArrayShape;
use Xgbnl\Bearer\Contracts\Authenticatable;
use Xgbnl\Bearer\Contracts\Provider\Provider;
use Xgbnl\Bearer\Enum\Date;
use Xgbnl\Bearer\Exception\BearerException;
use RedisException;

class BearerGuard extends Bearer
{
    /**
     * @throws Exception\BearerException
     */
    public function logout(): void
    {
        if (is_null($this->user)) {
            throw new BearerException('请登录后再使用注销功能', 403);
        }

        $this->forgeToken();
    }

    public function expires(): bool
    {
        return $this->redis->ttl($this->tokenKey($this->getTokenForRequest())) <= 360;
    }

    /**
     * @throws BearerException
     */
    #[ArrayShape(['bearer_token' => "string", 'token_type' => "string"])]
    public function login(Model|Authenticatable $user): array
    {
        $token = $this->createToken();

        $bcrypt = $this->bcrypt($token);

        $tokenKey = $this->tokenKey($token);

        try {
            $this->redis->set($tokenKey, json_encode(
                    ['token' => $bcrypt, 'id' => $user->getModelIdentifier()],
                    JSON_UNESCAPED_UNICODE)
            );
        } catch (RedisException $e) {
            throw new BearerException('登录失败，添加令牌缓存时出错: [ ' . $e->getMessage() . ' ]');
        }

        // 过期时间再延长6分钟，当小于6分钟时说明token已过期
        $this->redis->expire($tokenKey, Date::TWO_DAYS->value + 360);

        return [
            'bearer_token' => $token,
            'token_type'   => 'Bearer',
        ];
    }

    public function getProvider(): Provider
    {
        return $this->provider;
    }
}
