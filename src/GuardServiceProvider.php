<?php

namespace Xgbnl\Guard;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\ServiceProvider;
use Xgbnl\Guard\Commands\GuardCommand;
use Xgbnl\Guard\Contracts\Authenticatable;
use Xgbnl\Guard\Contracts\Factory;
use Xgbnl\Guard\Services\GuardManager;
use Xgbnl\Guard\Token\AppConfig;

class GuardServiceProvider extends ServiceProvider
{
    protected array $commands = [
        GuardCommand::class,
    ];

    public function register(): void
    {
        $this->registerAuthenticator();
        $this->registerUserResolver();

        $this->registerAppConfig();
    }

    protected function registerAuthenticator(): void
    {
        $this->app->singleton(Factory::class, fn($app) => new GuardManager($app));

        $this->app->singleton('guard.driver', fn($app) => $app[Factory::class]->guard());
    }

    protected function registerAppConfig(): void
    {
        if (!empty($this->app['config']['guard.security']) && !empty($this->app['config']['guard.expiration'])) {

            AppConfig::init()
                ->configure($this->app['config']['guard.security'])
                ->configure(['expiration' => $this->app['config']['guard.expiration']]);
        }
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
        $this->publishes([__DIR__ . '/Config/guard.php' => config_path('guard.php')]);
        $this->publishes([__DIR__ . '/Middleware/GuardMiddleware.php' => app_path('Http/Middleware/GuardMiddleware.php')]);

        $this->commands($this->commands);
    }

    public function provides(): array
    {
        return ['guard'];
    }
}
