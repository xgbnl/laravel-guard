<?php

declare(strict_types=1);

namespace Xgbnl\Bearer;

use App\Enums\GuardEnum;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Xgbnl\Bearer\Contracts\GuardContact;
use Xgbnl\Bearer\Traits\GuardHelpers;

abstract class Bearer implements GuardContact
{
    use GuardHelpers;

    protected readonly UserProvider $provider;
    protected readonly Request      $request;

    protected readonly string $inputKey;
    protected readonly string $storageKey;

    protected readonly bool $hash;
    protected readonly int  $expire;

    protected Model|Authenticatable|null $user = null;

    public function __construct(
        UserProvider $provider,
        Request      $request,
        string       $inputKey = 'bearer_token',
        string       $storageKey = 'bearer_token',
        bool         $hash = false,
        int          $expire = 60,
    )
    {
        $this->provider = $provider;
        $this->inputKey = $inputKey;
        $this->request = $request;
        $this->storageKey = $storageKey;
        $this->hash = $hash;
        $this->expire = $expire;
    }

    final public function user(): Model|Authenticatable|null
    {
        if (!is_null($this->user)) {
            return $this->user;
        }

        if (is_null($accessToken = $this->getTokenForRequest())) {
            return null;
        }

        return $this->user = $this->provider->retrieveByCredentials([
           $this->storageKey => $this->hash ? hash('sha256',$accessToken): $accessToken
        ]);
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
}
