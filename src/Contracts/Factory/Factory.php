<?php

namespace Xgbnl\Guard\Contracts\Factory;

use Xgbnl\Guard\Contracts\Guard\Guard;

interface Factory
{
    /**
     * 返回一个守卫器实例
     *
     * Return new a guard instance.
     * @param string $role
     * @param string|array|null $relations
     * @return Guard
     */
    public function guard(string $role, string|array|null $relations): Guard;

    /**
     * 定义应该使用的默认守卫
     *
     * Defines the default guard that should be used.
     * @param string $name
     * @return void
     */
    public function shouldUse(string $name): void;
}
