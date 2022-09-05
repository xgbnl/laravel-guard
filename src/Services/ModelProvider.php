<?php

declare(strict_types=1);

namespace Xgbnl\Guard\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Xgbnl\Guard\Contracts\Authenticatable;
use Xgbnl\Guard\Exception\GuardException;

class ModelProvider
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

    /**
     * 通过id检索用户
     * @param int $id
     * @return Authenticatable|null
     */
    public function retrieveById(int $id): ?Authenticatable
    {
        return $this->resolve()->find($id);
    }

    /**
     * 解析模型
     * @return Builder
     */
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

    /**
     * 获取提供者名称
     * @return string
     */
    public function getProviderName(): string
    {
        return $this->provider;
    }
}
