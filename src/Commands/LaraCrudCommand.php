<?php

namespace LaraCrud\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class LaraCrudCommand extends Command
{
    protected $signature = 'lara:crud {model} {--directory= : Specify the directory for CRUD generation}';
    protected $description = 'Generate model, migration, request, validation, route, and service for CRUD';

    public function handle()
    {
        $modelName = $this->argument('model');
        $pluralModelName = Str::plural(strtolower($modelName));
        $directory = $this->option('directory');

        // Use the Laravel 'app' directory as the default if no directory is specified
        $defaultDirectory = app_path();

        // Check if the specified directory exists; if not, use the default directory
        if ($directory && !is_dir($directory)) {
            $this->error("The specified directory '{$directory}' does not exist. Using default directory: {$defaultDirectory}");
            $directory = $defaultDirectory;
        }

        $this->generateModel($modelName, $directory);
        $this->generateMigration($modelName, $pluralModelName, $directory);
        $this->generateRequest($modelName, $directory);
        $this->generateValidation("App\Http\Requests\\{$modelName}Request", $directory);
        $this->appendRoute($modelName, $pluralModelName, $directory);
        $this->generateService($modelName, $directory);
        $this->includeDemoControllerContent($modelName, $directory);

        $this->info('CRUD code (excluding controller) generated successfully!');
    }

    protected function generateModel($modelName)
    {
        Artisan::call('make:model', ['name' => $modelName]);
    }

    protected function generateMigration($modelName, $pluralModelName)
    {
        Artisan::call('make:migration', [
            'name' => "create_{$pluralModelName}_table",
            '--create' => $pluralModelName,
        ]);
    }

    protected function generateRequest($modelName)
    {
        Artisan::call('make:request', ['name' => "{$modelName}Request"]);
    }

    protected function generateValidation($requestClassName, $directory = null)
    {
        $defaultDirectory = app_path('Http/Requests');
        if ($directory && !is_dir($directory)) {
            $this->error("The specified directory '{$directory}' does not exist. Using default directory: {$defaultDirectory}");
            $directory = $defaultDirectory;
        }

        $validationContent = file_get_contents(resource_path('stubs/validation.stub'));

        $validationPath = $directory ? $directory . '/' . $requestClassName . '.php' : $defaultDirectory . '/' . $requestClassName . '.php';

        file_put_contents($validationPath, $validationContent);
    }
    protected function appendRoute($modelName, $pluralModelName)
    {
        $routeContent = "\nRoute::resource('{$pluralModelName}', '{$modelName}Controller');";
        file_put_contents(base_path('routes/web.php'), $routeContent, FILE_APPEND);
    }

    protected function generateService($modelName)
    {
        $serviceStubPath = resource_path('stubs/service.stub');

        if (!file_exists($serviceStubPath)) {
            $this->error("Service stub file not found: {$serviceStubPath}");
            return;
        }

        $serviceContent = file_get_contents($serviceStubPath);
        file_put_contents(app_path("Services/{$modelName}Service.php"), $serviceContent);
    }

    protected function includeDemoControllerContent($modelName)
    {
        $demoControllerStubPath = resource_path('stubs/demo_controller.stub');

        if (!file_exists($demoControllerStubPath)) {
            $this->error("Demo Controller stub file not found: {$demoControllerStubPath}");
            return;
        }

        $demoControllerContent = file_get_contents($demoControllerStubPath);
        file_put_contents(app_path("Http/Controllers/DemoController.php"), $demoControllerContent);
    }
}
