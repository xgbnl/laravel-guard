<?php

declare(strict_types=1);

namespace Xgbnl\Guard\Token;

use Exception;
use Redis;
use RedisException;
use Xgbnl\Guard\Guards\BaseGuard;
use http\Exception\RuntimeException;
use Xgbnl\Guard\Contracts\Authenticatable;
use Illuminate\Support\Facades\Redis as FacadeRedis;

class Token
{
    private readonly ?Redis    $client;
    private readonly BaseGuard $guard;

    public function __construct(string $connect, BaseGuard $guard)
    {
        $this->guard = $guard;

        try {
            $this->client = FacadeRedis::connection($connect)->client();
        } catch (RedisException $e) {
            throw new RuntimeException('初始化redis时错误[ ' . $e->getMessage() . ' ]请检查您的配置', 500);
        }
    }

    /**
     * 清除令牌
     * @return void
     */
    public function forget(): void
    {
        try {

            $this->client->del(Aes::decrypt($this->parseEncryptionToken()));

        } catch (RedisException $e) {
            throw new RuntimeException('从redis缓存清除令牌失败[ ' . $e->getMessage() . ' ]', 500);
        }
    }

    /**
     * 派发令牌
     * @param Authenticatable $user
     * @return string[]
     * @throws RedisException
     * @throws Exception
     */
    public function dispatch(Authenticatable $user): array
    {
        $key   = Generator::generateKey($user->getModelIdentifier(), $this->guard->provider()->getProviderName());
        $token = Generator::generateToken();

        $this->client->setex($key, AppConfig::init()->getExpiration(), $token);

        return [
            'token_type' => 'Bearer',
            'expires_in' => AppConfig::init()->getExpiration(),
            'scope'      => Aes::encrypt($key) . '.' . Aes::encrypt($token),
        ];
    }

    /**
     * 是否存在键
     * @return bool
     * @throws RedisException
     */
    public function hasKey(): bool
    {
        return $this->client->exists($this->parseEncryptionToken());
    }

    /**
     * 是否存在令牌
     * @throws RedisException
     */
    public function hasToken(): bool
    {
        return $this->client->get($this->parseEncryptionToken()) === $this->parseEncryptionToken('token');
    }

    /**
     * 解析加密令牌
     * @param string $apply
     * @return string
     */
    public function parseEncryptionToken(string $apply = 'header'): string
    {
        if (empty($this->guard->getTokenForRequest() || empty(explode('.', $this->guard->getTokenForRequest())))) {
            $this->resolveError();
        }

        $result = array_combine(['header', 'token'], explode('.', $this->guard->getTokenForRequest()));

        return Aes::decrypt($result[$apply]);
    }

    /**
     * 解析用户ID.
     * @return int
     */
    public function resolveIdentifier(): int
    {
        if (empty($body = explode(':', $this->parseEncryptionToken()))) {
            $this->resolveError();
        }

        return (int)array_pop($body);
    }

    private function resolveError()
    {
        throw new RuntimeException('无效令牌访问，请登录', 401);
    }
}