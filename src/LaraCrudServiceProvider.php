<?php

namespace LaraCrud\Providers;

use Illuminate\Support\ServiceProvider;

class LaraCrudServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/laracrud.php', 'laracrud');

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerCommands();
        $this->loadViewsFrom(__DIR__ . '/../Views', 'laracrud');
        $this->loadMigrationsFrom(__DIR__ . '/../Migrations');
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
        $this->publishes([
            __DIR__.'/../config/laracrud.php' => config_path('laracrud.php'),
        ], 'laracrud-config');
    }

    /**
     * Register the package commands.
     *
     * @return void
     */
    private function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \LaraCrud\Commands\LaraCrudCommand::class,
            ]);
        }
    }
}
