<?php

namespace Xgbnl\Guard\Token;

use http\Exception\RuntimeException;

class AppConfig
{
    static private self $appConfig;
    private array       $config = [];

    final protected const PASSPHRASE  = 'key';
    final protected const IV          = 'iv';
    final protected const CIPHER_ALGO = 'cipher_algo';
    final protected const EXPIRATION  = 'expiration';

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

    public function configure(array $config): self
    {
        $this->config = array_merge($this->config, $config);

        return $this;
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

    public function getExpiration(): int
    {
        return self::init()->getConfig(self::EXPIRATION);
    }

    private function getConfig(string $key): string
    {
        if (empty($this->config[$key])) {
            throw new RuntimeException($key . '缺少参数,请配置guard.php');
        }
        return $this->config[$key];
    }
}