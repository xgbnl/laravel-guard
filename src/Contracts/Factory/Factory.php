<?php

namespace Xgbnl\Bearer\Contracts\Factory;

use Xgbnl\Bearer\Contracts\Guard\Guard;

interface Factory
{
    /**
     * 返回一个守卫器实例
     *
     * Return new a guard instance.
     * @return Guard
     */
    public function guard(): Guard;

    /**
     * 定义应该使用的默认守卫
     *
     * Defines the default guard that should be used.
     * @param string $name
     * @return void
     */
    public function shouldUse(string $name): void;
}
