<?php

declare(strict_types=1);

namespace Xgbnl\Bearer\Services;

use http\Exception\InvalidArgumentException;
use Xgbnl\Bearer\Exception\BearerException;
use Xgbnl\Bearer\Traits\CreateUserProviders;
use Illuminate\Contracts\Foundation\Application;
use Xgbnl\Bearer\BearerGuard;
use Xgbnl\Bearer\Contracts\Factory\Factory;
use Xgbnl\Bearer\Contracts\Guard\GuardContact;
use Closure;

final class GuardManager implements Factory
{
    use CreateUserProviders;

    protected Application $app;

    protected array $customCreators = [];
    protected array $guards         = [];

    protected Closure $userResolver;

    public function __construct(Application $app)
    {
        $this->app = $app;

        $this->userResolver = fn($guard = null) => $this->guard($guard)->user();
    }

    public function guard(?string $role = null): GuardContact
    {
        $role = $role ?? $this->getDefaultDriver();

        return $this->guards[$role] ?? $this->guards[$role] = $this->resolve($role);
    }

    public function shouldUse(string $name): void
    {
        $name = $name ?? $this->getDefaultDriver();

        $this->setDefaultDriver($name);

        $this->userResolver = fn($guard = null) => $this->guard($guard)->user();
    }

    protected function resolve(string $name): GuardContact
    {
        $config = $this->getConfig($name);

        if (is_null($config)) {
            throw new InvalidArgumentException("守卫角色[{$name}] 没有定义");
        }

        return $this->createBearerDriver($config);
    }

    public function createBearerDriver(array $config): GuardContact
    {
        $guard = new BearerGuard(
            provider  : $this->createUserProvider($this->app, $config['provider']),
            request   : $this->app['request'],
            inputKey  : $config['input_key'] ?? 'bearer_token',
            encryption: $config['encryption'] ?? 'md5',
            expireIn  : $config['expires_in'] ?? 60,
            connect   : $this->app['config']['bearer.storage.redis.connect'],
            throttle  : $this->app['config']['bearer.storage.redis.throttle'],
        );

        $this->app->refresh('request', $guard, 'setRequest');

        return $guard;
    }

    protected function getConfig(string $name): ?array
    {
        return $this->app['config']["bearer.roles.{$name}"];
    }

    protected function getDefaultDriver(): string
    {
        return $this->app['config']['bearer.defaults.role'];
    }

    protected function setDefaultDriver(string $name): void
    {
        $this->app['config']['bearer.defaults.role'] = $name;
    }

    /**
     * 获取用户解析闭包
     * @return Closure|null
     */
    public function userResolver(): ?Closure
    {
        return $this->userResolver;
    }

    /**
     * 设置闭包引用到用户解析变量
     * @param Closure $userResolver
     * @return $this
     */
    public function resolveUsersUsing(Closure $userResolver): self
    {
        $this->userResolver = $userResolver;

        return $this;
    }
}
