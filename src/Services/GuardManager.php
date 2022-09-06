<?php

declare(strict_types=1);

namespace Xgbnl\Guard\Services;

use Closure;
use http\Exception\RuntimeException;
use Illuminate\Contracts\Foundation\Application;
use Xgbnl\Guard\Contracts\Factory;
use Xgbnl\Guard\Guards\Guard;
use Xgbnl\Guard\Contracts\Guards\GuardContact;
use Xgbnl\Guard\Contracts\Guards\StatefulGuard;
use Xgbnl\Guard\Contracts\Guards\ValidatorGuard;

class GuardManager implements Factory
{

    protected Application $app;
    protected array       $guards = [];

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function guard(string $role = null): GuardContact|StatefulGuard|ValidatorGuard
    {
        $role = $role ?? $this->getDefaultRole();

        return $this->guards[$role] ?? $this->guards[$role] = $this->resolve($role);
    }

    public function shouldUse(string $name): void
    {
        $name = $name ?? $this->getDefaultRole();

        $this->setDefaultRole($name);
    }

    protected function resolve(string $name): GuardContact|StatefulGuard|ValidatorGuard
    {
        $config = $this->getConfig($name);

        if (is_null($config)) {
            throw new RuntimeException('守卫角色[ ' . $name . ' ]未定义');
        }

        return $this->createGuardDriver($config);
    }

    public function createGuardDriver(array $config): GuardContact|StatefulGuard|ValidatorGuard
    {
        $guard = new Guard(
            provider: $this->createModelProvider($config['provider']),
            request : $this->app['request'],
            inputKey: $config['input_key'] ?? 'access_token',
            connect : $this->getRedisConnect(),
        );

        $this->app->refresh('request', $guard, 'setRequest');

        return $guard;
    }

    protected function getConfig(string $name): ?array
    {
        return $this->app['config']["guard.roles.{$name}"];
    }

    protected function getDefaultRole(): string
    {
        return $this->app['config']['guard.defaults.role'];
    }

    protected function setDefaultRole(string $name): void
    {
        $this->app['config']['guard.defaults.role'] = $name;
    }

    private function getRedisConnect(): string
    {
        return $this->app['config']['guard.store.redis.connect'] ?? 'default';
    }

    protected function createModelProvider(string $provider): ModelProvider
    {
        if (empty($this->app['config']["guard.providers.{$provider}"])) {
            throw new RuntimeException('无法完模型提供者实例化，请检查您的配置文件guard.php');
        }

        return new ModelProvider($this->app['config']["guard.providers.{$provider}"], $provider);
    }
}
