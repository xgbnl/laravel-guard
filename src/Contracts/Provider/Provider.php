<?php

namespace Xgbnl\Bearer\Contracts\Provider;

use Illuminate\Database\Eloquent\Builder;
use Xgbnl\Bearer\Contracts\Authenticatable;

interface Provider
{
    /**
     * 按ID检索并获取用户
     * @param int $id
     * @return Authenticatable|null
     */
    public function retrieveById(int $id): ?Authenticatable;

    /**
     * 解析返回一个构造查询器
     * @return Builder
     */
    public function resolve(): Builder;

    /**
     * 获取守卫名
     * @return string
     */
    public function getProviderName(): string;
}
