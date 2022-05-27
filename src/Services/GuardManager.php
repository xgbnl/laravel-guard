<?php

declare(strict_types=1);

namespace Xgbnl\Bearer\Services;

use http\Exception\InvalidArgumentException;
use Illuminate\Auth\CreatesUserProviders;
use Illuminate\Contracts\Foundation\Application;
use Xgbnl\Bearer\BearerGuard;
use Xgbnl\Bearer\Contracts\FactoryContract;
use Xgbnl\Bearer\Contracts\GuardContact;
use Closure;

final class GuardManager implements FactoryContract
{
    use CreatesUserProviders;

    protected Application $app;

    protected array $customCreators = [];
    protected array $guards         = [];

    protected Closure $userResolver;

    public function __construct(Application $app)
    {
        $this->app = $app;

        $this->userResolver = fn($guard = null) => $this->guard($guard)->user();
    }

    /**
     * Return new a guard instance.
     * @param string|null $name
     * @return GuardContact
     */
    public function guard(?string $name = null): GuardContact
    {
        $name = $name ?? $this->getDefaultDriver();

        return $this->guards[$name] ?? $this->guards[$name] = $this->resolve($name);
    }

    /**
     * By guard name resolve driver.
     * @param string $name
     * @return GuardContact
     */
    protected function resolve(string $name): GuardContact
    {
        $config = $this->getConfig($name);

        if (is_null($config)) {
            throw new InvalidArgumentException("守卫 [{$name}] 没有定义");
        }

        if (isset($this->customCreators[$config['driver']])) {
            return $this->callCustomCreator($name, $config);
        }

        $driverMethod = 'create' . ucfirst($config['driver']) . 'Driver';

        if (method_exists($this, $driverMethod)) {
            return $this->{$driverMethod}($name, $config);
        }

        throw new InvalidArgumentException("未定义守卫 [{$name}] 的承载驱动程序 [{$config['driver']}]");
    }

    /**
     * 调用自定义驱动创建者
     * @param string $name
     * @param array $config
     * @return GuardContact
     */
    protected function callCustomCreator(string $name, array $config): GuardContact
    {
        return $this->customCreators[$config['driver']]($this->app, $name, $config);
    }

    /**
     * 创建基于 Bearer 守卫的身份验证防护
     * @param string $name
     * @param array $config
     * @return GuardContact
     */
    public function createBearerDriver(string $name, array $config): GuardContact
    {
        $guard = new BearerGuard(
            $this->createUserProvider($config['provider'] ?? null),
            $this->app['request'],
            $config['input_key'] ?? 'access_token',
            $config['storage_key'] ?? 'bearer_token',
            $config['hash'] ?? false,
        );

        $this->app->refresh('request', $guard, 'setRequest');

        return $guard;
    }

    protected function getConfig(string $name): ?array
    {
        return $this->app['config']["bearer.roles.{$name}"];
    }

    /**
     * 获取默认的身份验证驱动程序名称
     * @return string
     */
    protected function getDefaultDriver(): string
    {
        return $this->app['config']['bearer.defaults.role'];
    }

    /**
     * 设置工厂应提供的默认保护驱动程序。
     * @param string $name
     * @return void
     */
    public function shouldUse(string $name): void
    {
        $name = $name ?? $this->getDefaultDriver();

        $this->setDefaultDriver($name);

        $this->userResolver = fn($guard = null) => $this->guard($guard)->user();
    }

    /**
     * 为默认的守卫角色设置驱动
     * @param string $name
     * @return void
     */
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

    /**
     * 动态调用方法时触发
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        return $this->guard()->{$method}(...$parameters);
    }
}
