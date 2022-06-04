<?php

declare(strict_types=1);

namespace Xgbnl\Bearer\Support;

use Illuminate\Database\Eloquent\Model;
use Xgbnl\Bearer\Contracts\Authenticatable;

final class ObjectWatcher
{
    public array $models = [];

    private static self $instance;

    private function __construct()
    {
    }

    private static function instance(): self
    {
        if (empty(self::instance())) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    static private function globalKey(Model|Authenticatable $user): string
    {
        return get_class($user) . '.' . $user->getModelIdentifier();
    }

    static public function addModels(Model|Authenticatable $user): void
    {
        $inst = self::instance();

        $inst->models[self::globalKey($user)] = $user;
    }

    static public function modelExists(Model|Authenticatable $user): Model|Authenticatable|null
    {
        $inst = self::instance();

        if (isset($inst->models[self::globalKey($user)])) {
            return $inst->models[self::globalKey($user)];
        }

        return null;
    }

}