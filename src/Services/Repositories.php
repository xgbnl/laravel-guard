<?php

declare(strict_types=1);

namespace Xgbnl\Bearer\Services;

use Redis;
use Exception;
use RedisException;
use Xgbnl\Bearer\Bearer;
use Xgbnl\Bearer\Enum\Date;
use Xgbnl\Bearer\Contracts\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis as FacadeRedis;

class Repositories
{
    private ?Redis $redis;

    private Generator $generator;
    private Bearer    $bearer;

    public function __construct(Generator $generator, string $connect = 'default')
    {
        $this->generator = $generator;

        try {
            $this->redis = FacadeRedis::connection($connect)->client();
        } catch (RedisException $e) {
            trigger(500, 'Error initializing redis,Please check your configure');
        }
    }

    /**
     * Clear cache.
     * @param string $token
     * @return void
     */
    public function forgeCache(string $token): void
    {
        try {
            $user = $this->fetchUser($this->generator->generateAuthKey($token));

            $this->redis->del([
                $this->generator->generateUserKey($user['uid'], $this->bearer->getTable()),
                $this->generator->generateAuthKey($token),
            ]);

        } catch (RedisException $e) {
            trigger(500, 'From redis cache clear token fail: [ ' . $e->getMessage() . ' ]');
        }
    }

    /**
     * Store user log in info.
     * @param Model|Authenticatable $model
     * @return array
     * @throws Exception
     */
    public function store(Model|Authenticatable $model): array
    {
        // Generate token and auth sign
        $token = $this->generator->generateToken();
        $sign  = $this->generator->generateSign($token);

        // For user generate key
        $userKey = $this->generator->generateUserKey($model->getModelIdentifier(), $this->bearer->getTable());
        $authKey = $this->generator->generateAuthKey($sign);

        if ($this->modelExists($model->getModelIdentifier(), $this->bearer->getTable())) {
            $oldSign = $this->redis->get($userKey);
            $this->redis->rename($oldSign, $authKey);
        }

        $this->doStore($model, $userKey, $authKey);

        return [
            'access_token' => $token,
            'token_type' => 'Bearer',
        ];
    }

    public function doStore(Model|Authenticatable $model, string $userKey, string $authKey): void
    {
        $expire = Date::TWO_DAYS->value + 360;

        try {
            $this->redis->setex($userKey, $expire, $authKey);

            $this->redis->setex(
                $authKey,
                $expire,
                json_encode([
                    'uid' => $model->getModelIdentifier(),
                    'device' => $this->bearer->getRequest()->userAgent(),
                    'ip' => $this->bearer->getRequest()->getClientIp(),
                ]),
            );
        } catch (RedisException $e) {
            trigger(500, 'Store user info fail !');
        }
    }

    /**
     * Determine token exists redis.
     * @param string $token
     * @return bool
     */
    public function tokenNotExists(string $token): bool
    {
        return !$this->redis->get($this->generator->generateAuthKey($token));
    }

    /**
     * Determine token if expires.
     * @param string $token
     * @return bool
     */
    public function tokenExpires(string $token): bool
    {
        return $this->redis->ttl($this->generator->generateAuthKey($token)) <= 360;
    }

    /**
     * Determine user key exists。
     * @param int $uid
     * @param string $provider
     * @return bool
     */
    private function modelExists(int $uid, string $provider): bool
    {
        return (bool)$this->redis->exists($this->generator->generateUserKey($uid, $provider));
    }

    /**
     * Get user.
     * @param string $token
     * @return array
     */
    public function fetchUser(string $token): array
    {
        $data = $this->redis->get($this->generator->generateAuthKey($token));

        return json_decode($data, true);
    }

    /**
     * Verify that the current user ip and the ip of the cache record are the same
     * @return bool
     */
    public function validateClientIP(): bool
    {
        return $this->validate($this->bearer->getRequest()->getClientIp());
    }

    /**
     * Verify that the current user device and the device of the cache record are the same
     * @return bool
     */
    public function validateDevice(): bool
    {
        return $this->validate($this->bearer->getRequest()->userAgent());
    }

    /**
     * Validate
     * @param string $needle
     * @return bool]
     */
    private function validate(string $needle): bool
    {
        $user = $this->fetchUser($this->bearer->getTokenForRequest());

        return !in_array($needle, $user);
    }

    /**
     * Set Bearer Guard.
     * @param Bearer $bearer
     * @return void
     */
    public function setBearer(Bearer $bearer): void
    {
        $this->bearer = $bearer;
    }
}