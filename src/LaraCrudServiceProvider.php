<?php

namespace laracrud;

use Illuminate\Support\ServiceProvider;
use laracrud\Commands\LaraCrudCommand;

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
            __DIR__ . '/../resources/stubs' => resource_path('stubs'),
            __DIR__ . '/Repositories' => app_path('Repositories'),
            __DIR__ . '/Services' => app_path('Services'),
            __DIR__ . '/RepositoryRegisterProvider.php' => app_path('Providers/RepositoryRegisterProvider.php'),
        ], '[laracrud-publish');
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
                LaraCrudCommand::class,
            ]);
        }
    }
}