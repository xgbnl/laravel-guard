<?php

namespace Xgbnl\Guard\Token;

use http\Exception\RuntimeException;

class AppConfig
{
    static private self $appConfig;
    private array       $config = [];

    final public const PASSPHRASE  = 'key';
    final public const IV          = 'iv';
    final public const CIPHER_ALGO = 'cipher_algo';

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    public static function init(): self
    {
        return empty(self::$appConfig) ? self::$appConfig = new self() : self::$appConfig;
    }

    public function configure(array $config): void
    {
        $this->config = $config;
    }

    public function getPassphrase(): string
    {
        return self::init()->getConfig(self::PASSPHRASE);
    }

    public function getIv(): string
    {
        return self::init()->getConfig(self::IV);
    }

    public function getCipherAlgo(): string
    {
        return self::init()->getConfig(self::CIPHER_ALGO);
    }

    private function getConfig(string $key): string
    {
        if (empty($this->config[$key])) {
            throw new RuntimeException($key . '缺少参数,请配置guard.php');
        }
        return $this->config[$key];
    }
}