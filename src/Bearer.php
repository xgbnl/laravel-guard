<?php

declare(strict_types=1);

namespace Xgbnl\Bearer;

use Xgbnl\Bearer\Contracts\Authenticatable;
use Xgbnl\Bearer\Contracts\Provider\Provider;
use Illuminate\Http\Request;
use Xgbnl\Bearer\Contracts\Guard\GuardContact;
use Xgbnl\Bearer\Services\Repositories;
use Xgbnl\Bearer\Traits\GuardHelpers;

abstract class Bearer implements GuardContact
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

        $conditions = [
            is_null($accessToken = $this->getTokenForRequest()),
            $this->repositories->tokenNotExists($this->getTokenForRequest()),
            $this->repositories->tokenExpires($accessToken)
        ];

        foreach ($conditions as $condition) {
            if ($condition) {
                return null;
            }
        }

        $user = $this->provider->retrieveById($this->repositories->fetchUser($accessToken)['uid']);

        if (is_null($user)) {
            return null;
        }

        return $this->user = $user;
    }

    final protected function getTokenForRequest(): ?string
    {
        $tokens = [
            'query' => $this->request->query($this->inputKey),
            'input' => $this->request->input($this->inputKey),
            'header' => $this->request->bearerToken(),
        ];

        $accessToken = array_filter($tokens, fn($token) => !empty($token));

        return !empty($accessToken) ? array_shift($accessToken) : null;
    }

    /**
     * Get provider model belong table name
     * @return string
     */
    final public function getTable(): string
    {
        return $this->getProvider()->getProvider();
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
