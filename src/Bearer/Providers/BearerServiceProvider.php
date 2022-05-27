<?php

namespace Xgbnl\Bearer\Providers;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\ServiceProvider;
use Xgbnl\Bearer\Contracts\FactoryContract;
use Xgbnl\Bearer\Services\GuardManager;

class BearerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->registerAuthenticator();
        $this->registerUserResolver();
    }

    /**
     * Register guard services to app defer load
     * @return void
     */
    protected function registerAuthenticator(): void
    {
        $this->app->singleton('bearer', fn($app) => new GuardManager($app));

        $this->app->singleton(FactoryContract::class, fn($app) => $app['bearer']);

        $this->app->singleton('bearer.driver', fn($app) => $app['bearer']->guard());

    }

    protected function registerUserResolver()
    {
        $this->app->bind(
            Authenticatable::class,
            fn($app) => call_user_func($app[FactoryContract::class]->userResolver())
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
    }

    public function provides(): array
    {
        return ['bearer'];
    }
}
