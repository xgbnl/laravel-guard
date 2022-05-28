<?php

namespace Xgbnl\Bearer\Traits;

trait HasApiToken
{
    /**
     * 获取用户唯一标识符名称
     * @return string
     */
    public function getAuthIdentifierName(): string
    {
        return 'id';
    }

    /**
     * 获取用户唯一标识符
     * @return int
     */
    public function getAuthIdentifier(): int
    {
        return $this->id;
    }

    /**
     * 获取用户的密码
     * @return string|null
     */
    public function getAuthPassword(): ?string
    {
        return $this->password ?? null;
    }
}
