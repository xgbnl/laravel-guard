<?php

namespace Xgbnl\Guard\Token;

use Exception;

final class Generator
{
    static public function generateToken(int $length = 64): string
    {
        $alphabet = '';
        foreach (['A' => 'Z', 'a' => 'z', '0' => '10',] as $k => $v) {
            $alphabet .= implode('', range($k, $v));
        }
        unset($k, $v);

        $max = strlen($alphabet);

        $token = '';
        for ($i = 0; $i < $length; $i++) {
            $token .= $alphabet[random_int(0, $max - 1)];
        }

        return $token;
    }

    static public function generateKey(string|int $id, string $provider): string
    {
        return "auth:{$provider}:{$id}";
    }
}