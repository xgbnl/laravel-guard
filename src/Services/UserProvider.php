<?php

declare(strict_types=1);

namespace Xgbnl\Bearer\Services;

use Illuminate\Database\Eloquent\Model;
use Xgbnl\Bearer\Contracts\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Application;
use Xgbnl\Bearer\Contracts\Provider\Provider;
use Xgbnl\Bearer\Exception\BearerException;

class UserProvider implements Provider
{
    protected Application $app;
    protected string      $provider;

    public function __construct(Application $app, string $provider)
    {
        $this->app = $app;
        $this->provider = $provider;
    }

    /**
     * @throws BearerException
     */
    public function retrieveById(int $id): Authenticatable|Model|null
    {
        return $this->resolve()->find($id);
    }

    /**
     * @throws BearerException
     */
    public function resolve(): Builder
    {
        $provider = $this->app['config']["bearer.providers.{$this->provider}"];

        if (!isset($provider)) {
            throw new BearerException('Provider not exists');
        }

        if (!class_exists($provider)) {
            throw new BearerException('Model [ ' . $provider . ' ] not exists');
        }

        if (!is_subclass_of($provider, Model::class)) {
            throw new BearerException($provider . ' is not ' . Model::class . ' subclass');
        }

        return app($provider)::query();
    }

    public function getProvider(): string
    {
        return $this->provider;
    }
}
