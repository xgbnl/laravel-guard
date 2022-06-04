<?php

declare(strict_types=1);

namespace Xgbnl\Bearer\Services;

use Exception;
use JetBrains\PhpStorm\ArrayShape;
use Redis;
use RedisException;
use Xgbnl\Bearer\Contracts\Authenticatable;
use Xgbnl\Bearer\Contracts\Encryption;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis as FacadeRedis;
use Xgbnl\Bearer\Enum\Date;
use Xgbnl\Bearer\Support\ModelWatcher;

class Repositories
{
    private ?Redis $redis;

    private TokenService $encryption;

    public function __construct(Encryption $encryption, string $connect = 'default')
    {
        $this->encryption = $encryption;

        try {
            $this->redis = FacadeRedis::connection($connect)->client();
        } catch (RedisException $e) {
            trigger(500, 'Error initializing redis,Please check your configure');
        }
    }

    /**
     * @throws Exception
     */
    #[ArrayShape(['access_token' => "string", 'type' => "string"])]
    public function store(Model|Authenticatable $model): array
    {
        if ($this->modelExists($model)) {
            trigger(403, 'You are logged in,Please not repeat operate');
        }

        $token = $this->encryption->generateToken();

        $sign = $this->encryption->generateSign($token);

        try {
            $this->redis->set($this->encryption->generateKey($sign), $model->getModelIdentifier());
        } catch (RedisException $e) {
            trigger(500, 'Towards redis add key fail: [ ' . $e->getMessage() . ' ]');
        }

        $this->redis->expire($this->encryption->generateKey($sign), Date::TWO_DAYS->value + 360);

        $this->addModel($model);

        return [
            'access_token' => $token,
            'type'         => 'Bearer',
        ];
    }

    public function clearToken(string $token): void
    {
        try {
            $this->redis->del($this->encryption->generateKey($token));
        } catch (RedisException $e) {
            trigger(500, 'From redis cache clear token fail: [ ' . $e->getMessage() . ' ]');
        }
    }

    public function tokenNotExists(string $token): bool
    {
        return !$this->redis->get($this->encryption->generateKey($token));
    }

    public function fetchUserId(string $token): int
    {
        return (int)$this->redis->get($this->encryption->generateKey($token));
    }

    public function tokenExpires(string $token): bool
    {
        return $this->redis->ttl($this->encryption->generateKey($token) <= 360);
    }

    // Decorate functions
    private function addModel(Model|Authenticatable $model): void
    {
        ModelWatcher::addModel($model);
    }

    private function modelExists(Model|Authenticatable $model): bool
    {
        return ModelWatcher::modelExists($model);
    }
}