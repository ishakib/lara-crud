<?php

namespace laracrud;

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
        $this->mergeConfigFrom(__DIR__ . '/../config/laracrud.php', 'laracrud');

        $this->app->bind(LaraCrudService::class, function () {
            $service = new LaraCrudService();
            $service->setServiceProvider(app(LaraCrudServiceProvider::class));
            return $service;
        });
    }
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerCommands();

        $this->publishes([
            __DIR__ . '/../config/laracrud.php' => config_path('laracrud.php'),
        ], 'laracrud-config');

        $this->publishes([
            __DIR__ . '/../resources/stubs' => resource_path('stubs'),
        ], 'laracrud-assets');
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
                \laracrud\Commands\LaraCrudCommand::class,
            ]);
        }
    }
}