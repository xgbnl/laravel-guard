<?php

namespace Xgbnl\Guard\Contracts;

interface Authenticatable
{
    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getModelIdentifierName(): string;

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getModelIdentifier(): mixed;

    /**
     * Get the password for the user.
     *
     * @return string|null
     */
    public function getModelPassword(): ?string;

}
