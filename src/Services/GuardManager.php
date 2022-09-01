<?php

declare(strict_types=1);

namespace Xgbnl\Guard\Services;

use Closure;
use http\Exception\RuntimeException;
use Xgbnl\Guard\Guard;
use Xgbnl\Guard\Contracts\Factory\Factory;
use Xgbnl\Guard\Contracts\Guard\GuardContact;
use Xgbnl\Guard\Traits\CreateUserProviders;
use Illuminate\Contracts\Foundation\Application;

class GuardManager implements Factory
{
    use CreateUserProviders;

    protected Application $app;

    protected array $customCreators = [];
    protected array $guards         = [];

    protected Closure $userResolver;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function guard(string $role, string|array|null $relations = null): GuardContact
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

    protected function resolve(string $name, string|array|null $relations): GuardContact
    {
        $config = $this->getConfig($name);

        if (is_null($config)) {
            throw new RuntimeException('守卫角色[ '.$name.' ]未定义');
        }

        if (!isset($this->customCreators[$name])) {
            $this->callCustomCreators($name, $config, $relations);
        } else {
            return $this->customCreators[$name];
        }

        return $this->createguardDriver($config, $relations);
    }

    private function callCustomCreators(string $name, array $config, string|array|null $relations): void
    {
        $this->customCreators[$name] = $this->createguardDriver($config, $relations);
    }

    public function createguardDriver(array $config, string|array|null $relations): GuardContact
    {
        $guard = new Guard(
            provider: $this->createUserProvider($config['provider'], $relations),
            request: $this->app['request'],
            repositories: $this->getRepositories(),
            inputKey: $config['input_key'] ?? 'access_token',
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

    public function getRepositories(): Repositories
    {
        return new Repositories(new Generator(), $this->app['config']['guard.store.redis.connect'] ?? 'default');
    }
}
