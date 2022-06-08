<?php

declare(strict_types=1);

namespace Xgbnl\Bearer\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Xgbnl\Bearer\Contracts\Authenticatable;
use Xgbnl\Bearer\Contracts\Provider\Provider;

class UserProvider implements Provider
{
    protected string $modelClass;
    protected string $provider;

    public function __construct(string $modelClass, string $provider)
    {
        $this->modelClass = $modelClass;
        $this->provider   = $provider;
    }

    public function retrieveById(int $id): ?Authenticatable
    {
        return $this->resolve()->find($id);
    }

    public function resolve(): Builder
    {
        if (!class_exists($this->modelClass)) {
            trigger(500, 'Model [ ' . $this->modelClass . ' ] not exists');
        }

        if (!is_subclass_of($this->modelClass, Model::class)) {
            trigger(500, $this->modelClass . ' is not ' . Model::class . ' subclass');
        }

        return app($this->modelClass)::query();
    }

    public function getProviderName(): string
    {
        return $this->provider;
    }
}
