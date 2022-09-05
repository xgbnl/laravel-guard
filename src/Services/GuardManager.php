<?php

declare(strict_types=1);

namespace Xgbnl\Guard\Services;

use Closure;
use http\Exception\RuntimeException;
use Illuminate\Contracts\Foundation\Application;
use Xgbnl\Guard\Contracts\Factory;
use Xgbnl\Guard\Contracts\Guard\GuardContact;
use Xgbnl\Guard\Contracts\Guard\StatefulGuard;
use Xgbnl\Guard\Contracts\Guard\ValidatorGuard;
use Xgbnl\Guard\Guards\Guard;

class GuardManager implements Factory
{

    protected Application $app;

    protected array $customCreators = [];
    protected array $guards         = [];

    protected Closure $userResolver;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function guard(string $role, string|array|null $relations = null): GuardContact|StatefulGuard|ValidatorGuard
    {
        $role = $role ?? $this->getDefaultDriver();

        return $this->guards[$role] ?? $this->guards[$role] = $this->resolve($role, $relations);
    }

    public function shouldUse(string $name): void
    {
        $name = $name ?? $this->getDefaultDriver();

        $this->setDefaultDriver($name);

        $this->resolveUsersUsing(fn($guard = null, $relations = []) => $this->guard($guard, $relations)->user());
    }

    protected function resolve(string $name, string|array|null $relations): GuardContact|StatefulGuard|ValidatorGuard
    {
        $config = $this->getConfig($name);

        if (is_null($config)) {
            throw new RuntimeException('守卫角色[ ' . $name . ' ]未定义');
        }

        if (!isset($this->customCreators[$name])) {
            $this->callCustomCreators($name, $config, $relations);
        } else {
            return $this->customCreators[$name];
        }

        return $this->createGuardDriver($config, $relations);
    }

    private function callCustomCreators(string $name, array $config, string|array|null $relations): void
    {
        $this->customCreators[$name] = $this->createGuardDriver($config, $relations);
    }

    public function createGuardDriver(array $config, string|array|null $relations): GuardContact|StatefulGuard|ValidatorGuard
    {
        $guard = new Guard(
            provider: $this->createModelProvider($config['provider'], $relations),
            request: $this->app['request'],
            inputKey: $config['input_key'] ?? 'access_token',
            connect: $this->getRedisConnect(),
        );

        $this->app->refresh('request', $guard, 'setRequest');

        return $guard;
    }

    protected function getConfig(string $name): ?array
    {
        return $this->app['config']["guard.roles.{$name}"];
    }

    protected function getDefaultDriver(): string
    {
        return $this->app['config']['guard.defaults.role'];
    }

    protected function setDefaultDriver(string $name): void
    {
        $this->app['config']['guard.defaults.role'] = $name;
    }

    public function userResolver(): ?Closure
    {
        return $this->userResolver;
    }

    public function resolveUsersUsing(Closure $userResolver): self
    {
        $this->userResolver = $userResolver;

        return $this;
    }

    private function getRedisConnect(): string
    {
        return $this->app['config']['guard.store.redis.connect'] ?? 'default';
    }

    protected function createModelProvider(string $provider, string|array|null $relations): ModelProvider
    {
        if (empty($this->app['config']["guard.providers.{$provider}"])) {
            throw new RuntimeException('无法完模型提供者实例化，请检查您的配置文件guard.php');
        }

        return new ModelProvider($this->app['config']["guard.providers.{$provider}"], $provider, $relations);
    }
}
