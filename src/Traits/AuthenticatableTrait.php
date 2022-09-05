<?php

namespace Xgbnl\Guard\Traits;

trait AuthenticatableTrait
{
    /**
     * 获取用户唯一标识符名称
     * @return string
     */
    public function getModelIdentifierName(): string
    {
        return 'id';
    }

    /**
     * 获取用户唯一标识符
     * @return int
     */
    public function getModelIdentifier(): int
    {
        return $this->id;
    }

    /**
     * 获取用户的密码
     * @return string|null
     */
    public function getModelPassword(): ?string
    {
        return $this->password ?? null;
    }
}
