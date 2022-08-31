<?php

declare(strict_types=1);

namespace Xgbnl\Bearer\Services;

use Closure;
use Xgbnl\Bearer\BearerGuard;
use Xgbnl\Bearer\Contracts\Factory\Factory;
use Xgbnl\Bearer\Contracts\Guard\GuardContact;
use Xgbnl\Bearer\Exception\BearerException;
use Xgbnl\Bearer\Traits\CreateUserProviders;
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

        $this->userResolver = fn($guard = null) => $this->guard($guard)->user();
    }

    public function guard(?string $role = null,string|array $relations = null): GuardContact
    {
        $role = $role ?? $this->getDefaultDriver();

        return $this->guards[$role] ?? $this->guards[$role] = $this->resolve($role,$relations);
    }

    public function shouldUse(string $name): void
    {
        $name = $name ?? $this->getDefaultDriver();

        $this->setDefaultDriver($name);

        $this->userResolver = fn($guard = null) => $this->guard($guard)->user();
    }

    protected function resolve(string $name,string|array $relations): GuardContact
    {
        $config = $this->getConfig($name);

        if (is_null($config)) {
            trigger(500, 'Guard role [ ' . $name . ' ] not define.');
        }

        if (!isset($this->customCreators[$name])) {
            $this->callCustomCreators($name, $config,$relations);
        } else {
            return $this->customCreators[$name];
        }

        return $this->createBearerDriver($config,$relations);
    }

    private function callCustomCreators(string $name, array $config,string|array $relations = null): void
    {
        $this->customCreators[$name] = $this->createBearerDriver($config,$relations);
    }

    public function createBearerDriver(array $config,string|array $relations): GuardContact
    {
        $guard = new BearerGuard(
            provider    : $this->createUserProvider($config['provider'],$relations),
            request     : $this->app['request'],
            repositories: $this->getRepositories(),
            inputKey    : $config['input_key'] ?? 'access_token',
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
        return new Repositories(new Generator(), $this->app['config']['bearer.store.redis.connect'] ?? 'default');
    }
}
