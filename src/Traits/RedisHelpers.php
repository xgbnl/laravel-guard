<?php

namespace Xgbnl\Bearer\Traits;

use Illuminate\Support\Facades\Redis as FacadeRedis;
use Redis;
use Xgbnl\Bearer\Exception\BearerException;
use RedisException;

trait RedisHelpers
{
    protected ?Redis $redis = null;

    protected ?string $token = null;

    /**
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
     * @throws BearerException
     */
    protected function forgeToken(): void
    {
        try {
            $this->redis->del([$this->tokenKey($this->getTokenForRequest())]);
        } catch (RedisException $e) {
            throw new BearerException('清除令牌缓存失败: [ ' . $e->getMessage() . ' ]');
        }
    }

    protected function tokenKey(string $token): string
    {
        return 'sys:user:token' . $token;
    }

    final protected function createToken(int $length = 64): string
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

    final protected function bcrypt(string $token): string
    {
        return match ($this->encryption) {
            'hash' => hash('sha256', $token),
            'md5'  => hash('md5', $token),
        };
    }
}
