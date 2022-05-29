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
    protected readonly string $encryption;
    protected readonly int    $expireIn;

    protected string $connect;
    protected int    $throttle;

    protected Authenticatable|null $user = null;

    public function __construct(
        Provider $provider,
        Request  $request,
        string   $inputKey,
        string   $encryption,
        int      $expireIn,
        string   $connect,
        int      $throttle,
    )
    {
        $this->provider = $provider;
        $this->inputKey = $inputKey;
        $this->request = $request;
        $this->encryption = $encryption;
        $this->expireIn = $expireIn;

        // init redis
        $this->connect = $connect;
        $this->throttle = $throttle;

        $this->configure($connect);
    }

    final public function user(): Authenticatable|null
    {
        if (!is_null($this->user)) {
            return $this->user;
        }

        if (is_null($accessToken = $this->getTokenForRequest())) {
            return null;
        }

        if (empty($id = $this->redis->get($this->bcrypt($accessToken)))) {
            return null;
        }

        if (is_null($user = $this->provider->retrieveById((int)$id))) {
            return null;
        }

        return $this->user = $user;
    }

    private function getTokenForRequest(): ?string
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

    final protected function createToken(int $length = 64): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $alphabet .= 'abcdefghijklmnopqrstuvwxyz';
        $alphabet .= '0123456789';

        $max = strlen($alphabet);

        for ($i = 0; $i < $length; $i++) {
            $this->token .= $alphabet[random_int(0, $max - 1)];
        }

        return $this->token;
    }

    final protected function bcrypt(string $token): string
    {
        return match ($this->encryption) {
            'hash' => hash('sha256', $token),
            'md5'  => hash('md5', $token),
        };
    }
}
