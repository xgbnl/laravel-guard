<?php

declare(strict_types=1);

namespace Xgbnl\Bearer;

use Xgbnl\Bearer\Contracts\Authenticatable;
use Xgbnl\Bearer\Contracts\Provider\Provider;
use Illuminate\Http\Request;
use Xgbnl\Bearer\Contracts\Guard\GuardContact;
use Xgbnl\Bearer\Exception\BearerException;
use Xgbnl\Bearer\Traits\GuardHelpers;
use Xgbnl\Bearer\Traits\RedisHelpers;

abstract class Bearer implements GuardContact
{
    use GuardHelpers, RedisHelpers;

    protected readonly Provider $provider;
    protected Request           $request;

    protected readonly string $inputKey;
    protected readonly string $encryption;

    protected Authenticatable|null $user = null;

    /**
     * @throws BearerException
     */
    public function __construct(
        Provider $provider,
        Request  $request,
        string   $inputKey,
        string   $encryption,
        string   $connect,
    )
    {
        $this->provider = $provider;
        $this->inputKey = $inputKey;
        $this->request = $request;
        $this->encryption = $encryption;

        // init redis
        $this->configure($this->connect = $connect ?? $this->connect);
    }

    final public function user(): Authenticatable|null
    {
        if (!is_null($this->user)) {
            return $this->user;
        }

        if (is_null($accessToken = $this->getTokenForRequest())) {
            return null;
        }

        if ($this->expiresIn()) {
            return null;
        }

        if ($this->tokenExists($accessToken)) {
            return null;
        }

        if (is_null($user = $this->provider->retrieveById($this->fetchUserId($accessToken)))) {
            return null;
        }

        return $this->user = $user;
    }

    final protected function getTokenForRequest(): ?string
    {
        $tokens = [
            'query'  => $this->request->query($this->inputKey),
            'input'  => $this->request->input($this->inputKey),
            'header' => $this->request->bearerToken(),
        ];

        $accessToken = array_filter($tokens, fn($token) => !empty($token));

        return !empty($accessToken) ? array_shift($accessToken) : null;
    }

    final public function setRequest(Request $request)
    {
        $this->request = $request;
    }
}
