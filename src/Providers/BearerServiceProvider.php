<?php

namespace Xgbnl\Bearer\Providers;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\ServiceProvider;
use Xgbnl\Bearer\Commands\BearerCommand;
use Xgbnl\Bearer\Contracts\Factory\Factory as FactoryContract;
use Xgbnl\Bearer\Services\GuardManager;

class BearerServiceProvider extends ServiceProvider
{
    protected array $commands = [
            BearerCommand::class,
        ];

    /**
     * Register services.
     *
     * @return void
     * @throws BindingResolutionException
     */
    public function register(): void
    {
        $this->registerAuthenticator();
        $this->registerUserResolver();
    }

    /**
     * Register guard services to app defer load
     * @return void
     * @throws BindingResolutionException
     */
    protected function registerAuthenticator(): void
    {
        $this->app->singleton('bearer', fn($app) => new GuardManager($app));

        $this->app->singleton(FactoryContract::class, fn($app) => $app['bearer']);

        $this->app->singleton('bearer.driver', fn($app) => $app['bearer']->guard());
    }

    protected function registerUserResolver(): void
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
    public function boot(): void
    {
        $this->publishes([dirname(__DIR__) . '/config/bearer.php' => config_path('bearer.php')], 'config');

        $this->installCommand($this->commands);
    }

    protected function installCommand(array $commands): void
    {
        $this->commands($commands);
    }

    public function provides(): array
    {
        return ['bearer'];
    }
}
