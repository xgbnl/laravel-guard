<?php

namespace Xgbnl\Guard;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\ServiceProvider;
use Xgbnl\Guard\Commands\GuardCommand;
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
        $this->registerConfigure();
    }

    protected function registerAuthenticator(): void
    {
        $this->app->singleton(Factory::class, fn($app) => new GuardManager($app));
    }

    protected function registerConfigure(): void
    {
        if ($this->hasGuardConfig()) {

            AppConfig::init()->configure($this->getGuardSecurity())
                ->configure(['expiration' => $this->getGuardExpiration()]);
        }
    }

    protected function hasGuardConfig(): bool
    {
        return !empty($this->getGuardSecurity()) && !empty($this->getGuardExpiration());
    }

    protected function getGuardSecurity(): array
    {
        return $this->app['config']['guard.security'];
    }

    protected function getGuardExpiration(): int
    {
        return $this->app['config']['guard.expiration'];
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
