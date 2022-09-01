<?php

declare(strict_types=1);

namespace Xgbnl\Guard\Services;

use Xgbnl\Guard\Exception\GuardException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Xgbnl\Guard\Contracts\Authenticatable;
use Xgbnl\Guard\Contracts\Provider\Provider;

class UserProvider implements Provider
{
    protected string            $modelClass;
    protected string            $provider;
    protected string|array|null $relations;

    public function __construct(string $modelClass, string $provider, string|array|null $relations)
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
            throw new GuardException('模型 [ ' . $this->modelClass . ' ] 不存在',500);
        }

        if (!is_subclass_of($this->modelClass, Model::class)) {
            throw new GuardException('模型[ '.$this->modelClass.' ]没有继承或不是[ '.Model::class.' ]的子类',500);
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
