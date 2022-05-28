<?php

declare(strict_types=1);

namespace Xgbnl\Bearer;

use Xgbnl\Bearer\Contracts\Authenticatable;
use Xgbnl\Bearer\Contracts\Provider\Provider;
use Illuminate\Http\Request;
use Xgbnl\Bearer\Contracts\Guard\GuardContact;
use Xgbnl\Bearer\Traits\GuardHelpers;
use Xgbnl\Bearer\Traits\RedisHelpers;

abstract class Bearer implements GuardContact
{
    use GuardHelpers, RedisHelpers;

    protected readonly Provider $provider;
    protected Request           $request;

    protected readonly string $inputKey;
    protected readonly string $storageKey;

    protected readonly bool $hash;
    protected readonly int  $expire;

    protected Authenticatable|null $user = null;

    public function __construct(
        Provider $provider,
        Request  $request,
        string   $inputKey,
        string   $storageKey,
        bool     $hash,
        int      $expire,
    )
    {
        $this->provider = $provider;
        $this->inputKey = $inputKey;
        $this->request = $request;
        $this->storageKey = $storageKey;
        $this->hash = $hash;
        $this->expire = $expire;
    }

    final public function user(): Authenticatable|null
    {
        if (!is_null($this->user)) {
            return $this->user;
        }

        if (is_null($accessToken = $this->getTokenForRequest())) {
            return null;
        }

        return $this->user = $this->provider->retrieveById($this->id());
    }

    private function getTokenForRequest(): ?string
    {
        $tokens = [
            'query'    => $this->request->query($this->inputKey),
            'input'    => $this->request->input($this->inputKey),
            'auth'     => $this->request->bearerToken(),
            'password' => $this->request->getPassword(),
        ];

        $accessToken = array_filter($tokens, fn($token) => !empty($token));

        return !empty($accessToken) ? array_shift($accessToken) : null;
    }

    final public function setRequest(Request $request)
    {
        $this->request = $request;
    }
}
