<?php

declare(strict_types=1);

namespace Xgbnl\Guard\Guards;

use Illuminate\Http\Request;
use RedisException;
use Xgbnl\Guard\Token\Token;
use Xgbnl\Guard\Contracts\Authenticatable;
use Xgbnl\Guard\Contracts\Guards\GuardContact;
use Xgbnl\Guard\Services\ModelProvider;
use Xgbnl\Guard\Traits\GuardTrait;

abstract class BaseGuard implements GuardContact
{
    use GuardTrait;

    protected readonly ModelProvider $provider;
    protected ?Authenticatable       $user = null;

    private readonly string $inputKey;

    private Request $request;
    protected Token $token;

    public function __construct(ModelProvider $provider, Request $request, string $inputKey, string $connect)
    {
        $this->provider = $provider;
        $this->inputKey = $inputKey;
        $this->request  = $request;
        $this->token    = new Token($connect, $this);
    }

    final public function user(string|array $relations = null): Authenticatable|null
    {
        if (!is_null($this->user)) {
            return $this->user;
        }

        if (is_null($this->getTokenForRequest())) {
            return null;
        }

        if (!$this->token->hasKey()) {
            return null;
        }

        if ($this->token->hasKey() && !$this->token->hasToken()) {
            return null;
        }

        $user = $this->provider->retrieveById($this->token->resolveIdentifier(),$relations);

        return !is_null($user) ? $this->user = $user : null;
    }

    final public function getTokenForRequest(): ?string
    {
        if (!is_null($this->request->bearerToken())) {
            return $this->request->bearerToken();
        }

        if (!is_null($this->request->query($this->inputKey))) {
            return $this->request->query($this->inputKey);
        }

        if (!is_null($this->request->input($this->inputKey))) {
            return $this->request->input($this->inputKey);
        }

        return null;
    }

    final public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    final public function getRequest(): Request
    {
        return $this->request;
    }

    public function provider(): ModelProvider
    {
        return $this->provider;
    }
}