<?php

declare(strict_types=1);

namespace Xgbnl\Bearer\Support;

use Illuminate\Database\Eloquent\Model;
use Xgbnl\Bearer\Contracts\Authenticatable;

final class ModelWatcher
{
    public array $models = [];

    private static self $instance;

    private function __construct() {}

    private static function instance(): self
    {
        if (empty(self::instance())) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    static private function globalKey(Model|Authenticatable $model): string
    {
        return get_class($model) . '.' . $model->getModelIdentifier();
    }

    static public function addModel(Model|Authenticatable $model): void
    {
        $inst = self::instance();

        $inst->models[self::globalKey($model)] = $model;
    }

    static public function modelExists(Model|Authenticatable $model): bool
    {
        $inst = self::instance();

        return isset($inst->models[self::globalKey($model)]);
    }
}