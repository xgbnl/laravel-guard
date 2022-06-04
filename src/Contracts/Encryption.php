<?php

namespace Xgbnl\Bearer\Contracts;

interface Encryption
{
    /**
     * Generate a new token.
     * @return string
     */
    public function generateToken(): string;

    /**
     * Will be token generate sign.
     * @return string
     */
    public function generateSign(): string;
}