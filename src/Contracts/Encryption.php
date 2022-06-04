<?php

namespace Xgbnl\Bearer\Contracts;

interface Encryption
{
    /**
     * Generate a new token.
     * @param int $length
     * @return string
     */
    public function generateToken(int $length = 64): string;

    /**
     * Will be token generate sign.
     * @param string $token
     * @return string
     */
    public function generateSign(string $token): string;

    /**
     * Generate a new key.
     * @param string $token
     * @return string
     */
    public function generateKey(string $token): string;
}