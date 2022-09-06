<?php

namespace Xgbnl\Guard\Contracts;

interface Authenticatable
{
    /**
     * 获取模型标识符名称
     * @return string
     */
    public function getModelIdentifierName(): string;

    /**
     * 获取模型标识符
     * @return string|int
     */
    public function getModelIdentifier(): string|int;

    /**
     * 获取模型密码
     * @return string|null
     */
    public function getModelPassword(): ?string;

}
