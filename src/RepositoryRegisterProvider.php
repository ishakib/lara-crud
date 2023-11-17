<?php

namespace laracrud;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use function App\Providers\app_path;

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
        $baseNamespace = 'App\Repositories';
        $contractsNamespace = 'Contracts';

        $repositoriesPath = app_path('Repositories');
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
