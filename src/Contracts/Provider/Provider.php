<?php

namespace Xgbnl\Bearer\Contracts\Provider;

use Xgbnl\Bearer\Contracts\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

interface Provider
{
    /**
     * 按ID检索并获取用户
     * @param int $id
     * @return Model|Authenticatable|null
     */
    public function retrieveById(int $id): Authenticatable|Model|null;

    /**
     * 解析返回一个构造查询器
     * @return Builder
     */
    public function resolve():Builder;
}
