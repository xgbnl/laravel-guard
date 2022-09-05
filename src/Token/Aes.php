<?php

namespace Xgbnl\Guard\Token;

use Xgbnl\Guard\Enum\AesEnum;

final class Aes
{
    static public function encrypt(string $data): string
    {
        return base64_encode(openssl_encrypt(
            $data,
            AppConfig::init()->getCipherAlgo(),
            AppConfig::init()->getPassphrase(),
            OPENSSL_RAW_DATA,
            AppConfig::init()->getIv(),

        ));
    }

    static public function decrypt(string $data): string
    {
        return base64_decode(openssl_decrypt(
            $data,
            AppConfig::init()->getCipherAlgo(),
            AppConfig::init()->getPassphrase(),
            OPENSSL_RAW_DATA,
            AppConfig::init()->getIv(),
        ));
    }
}