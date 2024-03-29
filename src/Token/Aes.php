<?php

namespace Xgbnl\Guard\Token;

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
        return openssl_decrypt(
            base64_decode($data),
            AppConfig::init()->getCipherAlgo(),
            AppConfig::init()->getPassphrase(),
            OPENSSL_RAW_DATA,
            AppConfig::init()->getIv(),
        );
    }
}