<?php

namespace Xgbnl\Bearer\Services;

use Exception;
use Xgbnl\Bearer\Contracts\Encryption;

class TokenService implements Encryption
{
    private ?string $token = null;

    /**
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

    public function generateSign(string $token): string
    {
        return hash('sha256', $token);
    }

    public function generateKey(string $token): string
    {
        return 'sys:user:token' . $token;
    }
}