<?php

namespace Xgbnl\Bearer\Traits;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis as FacadeRedis;
use JetBrains\PhpStorm\ArrayShape;
use Redis;
use Xgbnl\Bearer\Contracts\Authenticatable;
use Xgbnl\Bearer\Enum\Date;
use Xgbnl\Bearer\Exception\BearerException;
use RedisException;

trait RedisHelpers
{
    protected ?Redis $redis = null;

    protected ?string $token = null;

    protected string $connect = 'default';

    private array $models = []; // Store model object library

    /**
     * When initializing configure redis connect.
     * @throws BearerException
     */
    protected function configure(string $name): void
    {
        try {
            $this->redis = FacadeRedis::connection($name)->client();
        } catch (RedisException $exception) {
            throw new BearerException(
                'Bearer守卫连接 redis 失败,[ ' . $exception->getMessage() . ' ] 请检查配置',
                500
            );
        }
    }

    /**
     * Clear redis.
     * @throws BearerException
     */
    protected function forgeToken(): void
    {
        try {
            $this->redis->del([$this->additionTokenHeader($this->getTokenForRequest())]);
        } catch (RedisException $e) {
            throw new BearerException('清除令牌缓存失败: [ ' . $e->getMessage() . ' ]', 403);
        }
    }

    /**
     * Generate redis key.
     * @param string $token
     * @return string
     */
    final protected function additionTokenHeader(string $token): string
    {
        return 'sys:user:token' . $token;
    }

    /**
     * Create a new token.
     * @param int $length
     * @return string
     * @throws Exception
     */
    private function createToken(int $length = 64): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $alphabet .= 'abcdefghijklmnopqrstuvwxyz';
        $alphabet .= '0123456789';

        $max = strlen($alphabet);

        for ($i = 0; $i < $length; $i++) {
            $this->token .= $alphabet[random_int(0, $max - 1)];
        }

        return $this->token;
    }

    /**
     * Encrypt token.
     * @param string $token
     * @return string
     */
    private function encryptToken(string $token): string
    {
        return match ($this->encryption) {
            'hash' => hash('sha256', $token),
            'md5'  => hash('md5', $token),
        };
    }

    /**
     * Store token to redis and login
     * @param Model|Authenticatable $user
     * @return array
     * @throws Exception
     */
    #[ArrayShape(['access_token' => "string", 'type' => "string"])]
    final protected function store(Model|Authenticatable $user): array
    {

        if (isset($this->models[$this->globleKey($user)])){
            throw new BearerException('已登录，请勿重复操作');
        }

        $originToken = $this->createToken();

        $encryption = $this->encryptToken($originToken);

        $key = $this->additionTokenHeader($originToken);

        try {
            $this->redis->set($key, json_encode(
                ['token' => $encryption, 'id' => $user->getModelIdentifier()],
                JSON_UNESCAPED_UNICODE));
        } catch (RedisException $e) {
            throw new BearerException('登录失败，添加令牌缓存时出错: [ ' . $e->getMessage() . ' ]');
        }

        $this->redis->expire($key, Date::TWO_DAYS->value + 360);

        $this->models[$this->globleKey($user)] = $user;

        return [
            'access_token' => $originToken,
            'type'         => 'Bearer',
        ];
    }

    /**
     * Set or get model key.
     */
    private function globleKey(Model|Authenticatable $user):string
    {
        return get_class($user).'.'.$user->getModelIdentifier();
    }

    /**
     * Check token if expires in .
     * @return bool
     */
    final protected function expiresIn(): bool
    {
        return $this->redis->ttl($this->additionTokenHeader($this->getTokenForRequest())) <= 360;
    }

    /**
     * Check token exists redis.
     * @param string $token
     * @return bool
     */
    private function tokenExists(string $token): bool
    {
        if (empty($cache = $this->redis->get($this->additionTokenHeader($token)))) {
            return false;
        }

        $data = json_decode($cache, true);

        return !in_array($this->encryptToken($token), $data);
    }

    private function fetchUserId(string $token): int
    {
        $data = $this->redis->get($this->additionTokenHeader($token));

        return (int)json_decode($data, true)['id'];
    }
}
