<?php

declare(strict_types=1);

namespace Xgbnl\Guard\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Xgbnl\Guard\Contracts\Authenticatable;
use Xgbnl\Guard\Exception\GuardException;

class ModelProvider
{
    protected string $modelClass;
    protected string $provider;

    public function __construct(string $modelClass, string $provider)
    {
        $this->modelClass = $modelClass;
        $this->provider   = $provider;
    }

    /**
     * 通过id检索用户
     * @param int $id
     * @param string|array|null $relations
     * @return Authenticatable|null
     */
    public function retrieveById(int $id, string|array|null $relations): ?Authenticatable
    {
        return empty($relations)
            ? $this->resolve()->find($id)
            : $this->resolve()->with($relations)->find($id);
    }

    /**
     * 解析模型
     * @return Builder
     */
    public function resolve(): Builder
    {
        if (!class_exists($this->modelClass)) {
            throw new GuardException('模型 [ ' . $this->modelClass . ' ] 不存在', 500);
        }

        if (!is_subclass_of($this->modelClass, Model::class)) {
            throw new GuardException('模型[ ' . $this->modelClass . ' ]没有继承或不是[ ' . Model::class . ' ]的子类', 500);
        }

        return app($this->modelClass)::query();
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
