<?php

namespace Xgbnl\Guard;

use Xgbnl\Guard\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\ServiceProvider;
use Xgbnl\Guard\Commands\GuardCommand;
use Xgbnl\Guard\Contracts\Factory\Factory;
use Xgbnl\Guard\Services\GuardManager;

class GuardServiceProvider extends ServiceProvider
{
    protected array $commands = [
            GuardCommand::class,
        ];

    public function register(): void
    {
        $this->registerAuthenticator();
        $this->registerUserResolver();
    }

    protected function registerAuthenticator(): void
    {
        $this->app->singleton(Factory::class, fn($app) => new GuardManager($app));

        $this->app->singleton('guard.driver', fn($app) => $app[Factory::class]->guard());
    }

    protected function registerUserResolver(): void
    {
        $this->app->bind(
            Authenticatable::class,
            fn($app) => call_user_func($app[Factory::class]->userResolver())
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->publishes([dirname(__DIR__) . '/config/guard.php' => config_path('guard.php')], 'config');

        $this->installCommand($this->commands);
    }

    protected function installCommand(array $commands): void
    {
        $this->commands($commands);
    }

    public function provides(): array
    {
        return ['guard'];
    }
}
