<?php

namespace laracrud\Services;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use laracrud\LaraCrudServiceProvider;

class LaraCrudService
{
    public $serviceProvider;
    protected $command;

    public function setCommand(Command $command)
    {
        $this->command = $command;
    }

    // Use method injection instead of constructor injection
    public function setServiceProvider(Command $command, LaraCrudServiceProvider $serviceProvider)
    {
        $this->serviceProvider = $serviceProvider;
    }

    public function getInteractiveInputs(): array
    {
        // Interactive prompt for the model name
        $modelName = $this->command->ask('Enter model name...');

        // Interactive prompt for specifying a directory
        $directoryChoice = $this->command->choice('Do you want to specify a directory where every file will be created?', ['y', 'n'], 'n');

        if ($directoryChoice === 'y') {
            // Interactive prompt for the directory name
            $directory = $this->command->ask('Enter directory name...');
        } else {
            $directory = null;
        }

        return [$modelName, $directory];
    }

    public function generateModel($modelName)
    {
        Artisan::call('make:model', ['name' => $modelName]);
    }

    public function accessBindModels(LaraCrudServiceProvider $serviceProvider, $modelName)
    {
        $serviceProvider->bindModels($modelName);
    }

    public function generateMigration($modelName, $pluralModelName)
    {
        Artisan::call('make:migration', [
            'name' => "create_{$pluralModelName}_table",
            '--create' => $pluralModelName,
        ]);
    }

    public function generateRequest($modelName)
    {
        Artisan::call('make:request', ['name' => "{$modelName}Request"]);
    }

    public function generateValidation($requestClassName, $directory = null)
    {
        // Use the Laravel 'app/Http/Requests' directory as the default if no directory is specified
        $defaultDirectory = app_path('Http/Requests');

        // Check if the specified directory exists; if not, use the default directory
        if ($directory && !is_dir($directory)) {
            $this->command->error("The specified directory '{$directory}' does not exist. Using default directory: {$defaultDirectory}");
            $directory = $defaultDirectory;
        }

        $stubFilePath = resource_path('stubs/validation.stub');

        // Check if the stub file exists
        if (!file_exists($stubFilePath)) {
            $this->command->error("Validation stub file not found: {$stubFilePath}");
            return;
        }

        $validationContent = file_get_contents($stubFilePath);

        $validationPath = $directory ? $directory . '/' . $requestClassName . '.php' : $defaultDirectory . '/' . $requestClassName . '.php';

        file_put_contents($validationPath, $validationContent);
    }

    public function appendRoute($modelName, $pluralModelName, $directory)
    {
        $modelKeyName = Str::camel(class_basename($modelName));
        $routeFilePath = base_path('routes/api.php');
        $existingRouteContent = File::exists($routeFilePath) ? File::get($routeFilePath) : '';

        // Define the controller name
        $modelNameController = "{$modelName}Controller";

        // Define the namespace
        $namespace = 'App\Http\Controllers';

        $apiResource = "Route::apiResource('$pluralModelName', '$namespace\\$modelNameController::class');";

        if (!Str::contains($existingRouteContent, $apiResource)) {
            File::append($routeFilePath, "\n$apiResource");
            $this->command->info("API resource route for '$modelName' added successfully.");
        } else {
            $this->command->info("API resource route for '$modelName' already exists.");
        }
    }


    public function generateService($modelName, $directory)
    {
        $servicesDirectory = app_path('Services');
        if (!is_dir($servicesDirectory)) {
            mkdir($servicesDirectory, 0755, true);
        }

        $serviceStubPath = resource_path('stubs/service.stub');

        if (!file_exists($serviceStubPath)) {
            $this->command->error("Service stub file not found: {$serviceStubPath}");
            return;
        }


        $servicePath = $directory
            ? $directory . '/' . $modelName . 'Service.php'
            : $servicesDirectory . '/' . $modelName . 'Service.php';

        $serviceContent = file_get_contents($serviceStubPath);
        file_put_contents($servicePath, $serviceContent);
    }

    public function includeDemoControllerContent($modelName, $directory)
    {
        // Define the model key name for implicit route model binding
        $modelKeyName = Str::camel(class_basename($modelName));

        // Define the controller class name
        $controllerName = "{$modelName}Controller";

        // Get the demo controller stub content
        $demoControllerStubPath = resource_path('stubs/controller.stub');

        if (!file_exists($demoControllerStubPath)) {
            $this->command->error("Demo Controller stub file not found: {$demoControllerStubPath}");
            return;
        }

        // Replace placeholders in the demo controller content with actual values
        $demoControllerContent = file_get_contents($demoControllerStubPath);
        $placeholders = ['{ModelName}', '{ModelKeyName}', '{ControllerName}'];
        $replacements = [$modelName, $modelKeyName, $controllerName];
        $demoControllerContent = str_replace($placeholders, $replacements, $demoControllerContent);

        // Determine the target path based on the specified directory or the default 'Http/Controllers' directory
        $controllerPath = $directory
            ? $directory . '/Http/Controllers/' . $controllerName . '.php'
            : app_path('Http/Controllers/') . $controllerName . '.php';

        // Ensure the target directory exists; if not, create it
        $targetDirectory = dirname($controllerPath);
        if (!is_dir($targetDirectory)) {
            mkdir($targetDirectory, 0755, true);
        }

        // Write the demo controller content to the target path
        file_put_contents($controllerPath, $demoControllerContent);

        $this->command->info("Demo Controller generated successfully: {$controllerPath}");
    }
}
