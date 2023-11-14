<?php

namespace LaraCrud\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\App;

class LaraCrudCommand extends Command
{
    protected $signature = 'lara:crud';
    protected $description = 'Generate model, migration, request, validation, route, and service for CRUD';

    public function handle()
    {
        // Get model name and directory from interactive prompts
        list($modelName, $directory) = $this->getInteractiveInputs();

        $pluralModelName = Str::plural(strtolower($modelName));

        $this->generateModel($modelName, $directory);
        $this->generateMigration($modelName, $pluralModelName, $directory);
        $this->generateRequest($modelName, $directory);
        $this->generateValidation("App\Http\Requests\\{$modelName}Request", $directory);
        $this->generateService($modelName, $directory);
        $this->includeDemoControllerContent($modelName, $directory);

        $this->appendRoute($modelName, $pluralModelName, $directory);
        $this->accessBindModels($modelName); // Include the accessBindModels method call
        $this->info('CRUD code (excluding controller) generated successfully!');
    }

    protected function getInteractiveInputs()
    {
        // Interactive prompt for the model name
        $modelName = $this->ask('Enter model name...');

        // Interactive prompt for specifying a directory
        $directoryChoice = $this->choice('Do you want to specify a directory where every file will be created?', ['y', 'n'], 'n');

        if ($directoryChoice === 'y') {
            // Interactive prompt for the directory name
            $directory = $this->ask('Enter directory name...');
        } else {
            $directory = null;
        }

        return [$modelName, $directory];
    }

    protected function generateModel($modelName)
    {
        Artisan::call('make:model', ['name' => $modelName]);
    }

    protected function accessBindModels($modelName)
    {
        $serviceProvider = App::make(\laracrud\LaraCrudServiceProvider::class);
        $serviceProvider->bindModels($modelName);
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
        // Use the Laravel 'app/Http/Requests' directory as the default if no directory is specified
        $defaultDirectory = app_path('Http/Requests');

        // Check if the specified directory exists; if not, use the default directory
        if ($directory && !is_dir($directory)) {
            $this->error("The specified directory '{$directory}' does not exist. Using default directory: {$defaultDirectory}");
            $directory = $defaultDirectory;
        }

        $stubFilePath = resource_path('stubs/validation.stub');

        // Check if the stub file exists
        if (!file_exists($stubFilePath)) {
            $this->error("Validation stub file not found: {$stubFilePath}");
            return;
        }

        $validationContent = file_get_contents($stubFilePath);

        $validationPath = $directory ? $directory . '/' . $requestClassName . '.php' : $defaultDirectory . '/' . $requestClassName . '.php';

        file_put_contents($validationPath, $validationContent);
    }

    protected function appendRoute($modelName, $pluralModelName, $directory)
    {
        // Define the model key name for implicit route model binding
        $modelKeyName = Str::camel(class_basename($modelName));

        $routeContent = "\nRoute::apiResource('{$pluralModelName}', '{$modelName}Controller')->parameters(['{$pluralModelName}' => '{$modelKeyName}']);";

        file_put_contents(base_path('routes/api.php'), $routeContent, FILE_APPEND);
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
