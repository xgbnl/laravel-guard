<?php

declare(strict_types=1);

namespace Xgbnl\Guard;

use Illuminate\Http\Request;
use Xgbnl\Guard\Contracts\Authenticatable;
use Xgbnl\Guard\Contracts\Provider\Provider;
use Xgbnl\Guard\Contracts\Guard\GuardContact;
use Xgbnl\Guard\Services\Repositories;
use Xgbnl\Guard\Traits\GuardHelpers;

abstract class BaseGuard implements GuardContact
{
    use GuardHelpers;

    protected readonly Provider $provider;
    protected Request           $request;

    protected Repositories $repositories;

    protected readonly string $inputKey;

    protected Authenticatable|null $user = null;

    public function __construct(Provider $provider, Request $request, Repositories $repositories, string $inputKey)
    {
        $this->provider     = $provider;
        $this->inputKey     = $inputKey;
        $this->request      = $request;
        $this->repositories = $repositories;

        $this->repositories->setBearer($this);
    }

    final public function user(): Authenticatable|null
    {
        if (!is_null($this->user)) {
            return $this->user;
        }

        if (is_null($accessToken = $this->getTokenForRequest())) {
            return null;
        }

        if ($this->repositories->tokenNotExists($accessToken)) {
            return null;
        }

        $user = $this->provider->retrieveById($this->repositories->fetchUser($accessToken)['uid']);

        if (is_null($user)) {
            return null;
        }

        return $this->user = $user;
    }

    final public function getTokenForRequest(): ?string
    {
        $tokens = [
            'query' => $this->request->query($this->inputKey),
            'input' => $this->request->input($this->inputKey),
            'header' => $this->request->bearerToken(),
        ];

        $accessToken = array_filter($tokens, fn($token) => !empty($token));

        return !empty($accessToken) ? array_shift($accessToken) : null;
    }

    final public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    final public function getRequest(): Request
    {
        return $this->request;
    }
}
