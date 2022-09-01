<?php

declare(strict_types=1);

namespace Xgbnl\Guard\Services;

use http\Exception\RuntimeException;
use Redis;
use RedisException;
use Xgbnl\Guard\BaseGuard;
use Xgbnl\Guard\Enum\Date;
use Xgbnl\Guard\Contracts\Authenticatable;
use Illuminate\Support\Facades\Redis as FacadeRedis;

class Repositories
{
    private ?Redis $redis;

    private Generator $generator;
    private BaseGuard $bearer;

    public function __construct(Generator $generator, string $connect)
    {
        $this->generator = $generator;

        try {
            $this->redis = FacadeRedis::connection($connect)->client();
        } catch (RedisException $e) {
            throw new RuntimeException('初始化redis时错误[ ' . $e->getMessage() . ' ]请检查您的配置', 500);
        }
    }

    public function forgeCache(string $token): void
    {
        try {
            $user = $this->fetchUser($token);

            $this->redis->del([
                $this->generator->generateUserKey($user['uid'], $this->bearer->getProvider()->getProviderName()),
                $this->generator->generateAuthKey($token),
            ]);

        } catch (RedisException $e) {
            throw new RuntimeException('从redis缓存清除令牌失败[ ' . $e->getMessage() . ' ]', 500);
        }
    }

    public function store(Authenticatable $user): array
    {
        // Generate token and auth sign
        $token = $this->generator->generateToken();

        // For user generate key
        $userKey = $this->generator->generateUserKey(
            $user->getModelIdentifier(),
            $this->bearer->getProvider()->getProviderName(),
        );

        $authKey = $this->generator->generateAuthKey($token);

        if ($this->modelExists($user->getModelIdentifier(), $this->bearer->getProvider()->getProviderName())) {
            $oldAuthKey = $this->redis->get($userKey);

            $this->redis->rename($oldAuthKey, $authKey);
        }

        $this->doStore($user, $userKey, $authKey);

        return [
            'access_token' => $token,
            'token_type'   => 'Bearer',
        ];
    }

    public function doStore(Authenticatable $user, string $userKey, string $authKey): void
    {
        $expire = Date::TWO_DAYS->value + 360;

        try {
            $this->redis->setex($userKey, $expire, $authKey);

            $this->redis->setex(
                $authKey,
                $expire,
                json_encode([
                    'uid'    => $user->getModelIdentifier(),
                    'device' => $this->bearer->getRequest()->userAgent(),
                    'ip'     => $this->bearer->getRequest()->getClientIp(),
                ]),
            );
        } catch (RedisException $e) {
            throw new RuntimeException ('储存用户信息失败[ ' . $e->getMessage() . ' ]', 500);
        }
    }

    public function tokenNotExists(string $token): bool
    {
        return !$this->redis->get($this->generator->generateAuthKey($token));
    }

    public function tokenExpires(string $token): bool
    {
        $timeout = $this->redis->ttl($this->generator->generateAuthKey($token));

        return is_numeric($timeout) && $timeout <= 360;
    }

    private function modelExists(int $uid, string $provider): bool
    {
        return (bool)$this->redis->exists($this->generator->generateUserKey($uid, $provider));
    }

    public function fetchUser(string $token): array
    {
        $data = $this->redis->get($this->generator->generateAuthKey($token));

        return json_decode($data, true);
    }

    public function setBearer(BaseGuard $bearer): void
    {
        $this->bearer = $bearer;
    }
}