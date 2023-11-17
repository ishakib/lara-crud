<?php

namespace App\Providers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class RepositoryRegisterProvider extends ServiceProvider
{

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->registerRepositories();
    }

    protected function registerRepositories(): void
    {
        $baseNamespace = Config::get('laracrud.repositories.namespace', 'App\Repositories');
        $contractsNamespace = Config::get('laracrud.repositories.contracts', 'Contracts');

        $repositoriesPath = app_path(Config::get('laracrud.repositories.path', 'Repositories'));
        $contractsPath = "{$repositoriesPath}/{$contractsNamespace}";

        $repositories = [];

        // Scan the Contracts directory for interfaces
        $interfaces = File::glob("{$contractsPath}/*.php");
        foreach ($interfaces as $interface) {
            $interfaceName = pathinfo($interface, PATHINFO_FILENAME);
            $repositoryName = str_replace("Interface", '', $interfaceName);

            $repositories[$repositoryName] = [
                'interface' => "{$baseNamespace}\\{$contractsNamespace}\\{$interfaceName}",
                'implementation' => "{$baseNamespace}\\Eloquent\\{$repositoryName}",
            ];
        }

        foreach ($repositories as $repositoryName => $repository) {
            $this->app->bind($repository['interface'], $repository['implementation']);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }

}
