<?php

declare(strict_types=1);

namespace Xgbnl\Bearer\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Xgbnl\Bearer\Contracts\Authenticatable;
use Xgbnl\Bearer\Contracts\Provider\Provider;

class UserProvider implements Provider
{
    protected string            $modelClass;
    protected string            $provider;
    protected string|array $relations;

    public function __construct(string $modelClass, string $provider, string|array $relations)
    {
        $this->modelClass = $modelClass;
        $this->provider = $provider;
        $this->relations = $relations;
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

        return empty($this->relations)
            ? app($this->modelClass)::query()
            : app($this->modelClass)::query()->with($this->relations);
    }

    public function getProviderName(): string
    {
        return $this->provider;
    }
}
