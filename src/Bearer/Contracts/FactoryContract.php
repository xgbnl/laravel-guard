<?php

namespace Xgbnl\Bearer\Contracts;

interface FactoryContract
{
    /**
     * Return a new guard instance.
     * @return Guard
     */
    public function guard(): Guard;

    /**
     * Defines the default guard that should be used.
     * @param string $name
     * @return void
     */
    public function shouldUse(string $name): void;
}
