<?php

namespace Xgbnl\Bearer\Services;

use Exception;

class Generator
{
    private ?string $token = null;

    /**
     * Crate a new token.
     * @throws Exception
     */
    public function generateToken(int $length = 64): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $alphabet .= 'abcdefghijklmnopqrstuvwxyz';
        $alphabet .= '0123456789';

        $max = strlen($alphabet);

        for ($i = 0; $i < $length; $i++) {
            $this->token .= $alphabet[random_int(0, $max - 1)];
        }

        return $this->token;
    }

    /**
     * Use token generate new a sign.
     * @param string $token
     * @return string
     */
    private function generateSign(string $token): string
    {
        return hash('sha256', $token);
    }

    /**
     * Generate user mark key.
     * @param int $uid
     * @param string $provider
     * @return string
     */
    public function generateUserKey(int $uid, string $provider): string
    {
        return "auth:{$uid}:{$provider}";
    }

    /**
     * Generate token sign and return auth key.
     * @param string $token
     * @return string
     */
    public function generateAuthKey(string $token): string
    {
        return "auth:{$this->generateSign($token)}:token";
    }
}