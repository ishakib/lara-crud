<?php

namespace laracrud\Services;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class LaraCrudService
{
    protected $command;

    public function setCommand(Command $command): void
    {
        $this->command = $command;
    }

    public function getInteractiveInputs(): array
    {
        $modelName = $this->command->ask('Enter model name...');
        $directoryChoice = $this->command->choice('Do you want to specify a directory where every file will be created?', ['y', 'n'], 'n');

        if ($directoryChoice === 'y') {
            $directory = $this->command->ask('Enter directory name...');
        } else {
            $directory = null;
        }

        return [$modelName, $directory];
    }

    public function generateModel($modelName): void
    {
        Artisan::call('make:model', ['name' => $modelName]);
    }

    public function generateMigration($modelName, $pluralModelName): void
    {
        Artisan::call('make:migration', [
            'name' => "create_{$pluralModelName}_table",
            '--create' => $pluralModelName,
        ]);
    }

    public function generateRequest($modelName): void
    {
        Artisan::call('make:request', ['name' => "{$modelName}Request"]);
    }

    public function generateValidation($requestClassName, $directory = null): void
    {
        $defaultDirectory = app_path('Http/Requests');

        if ($directory && !is_dir($directory)) {
            $this->command->error("The specified directory '{$directory}' does not exist. Using default directory: {$defaultDirectory}");
            $directory = $defaultDirectory;
        }

        $stubFilePath = resource_path('stubs/validation.stub');

        if (!file_exists($stubFilePath)) {
            $this->command->error("Validation stub file not found: {$stubFilePath}");
            return;
        }

        $validationContent = file_get_contents($stubFilePath);

        $validationPath = $directory ? $directory . '/' . $requestClassName . '.php' : $defaultDirectory . '/' . $requestClassName . '.php';

        file_put_contents($validationPath, $validationContent);
    }

    public function appendRoute($modelName, $pluralModelName, $directory): void
    {
        $modelKeyName = Str::camel(class_basename($modelName));
        $routeFilePath = base_path('routes/web.php');
        $existingRouteContent = File::exists($routeFilePath) ? File::get($routeFilePath) : '';

        $modelNameController = "{$modelName}Controller";
        $namespace = 'App\Http\Controllers';

        $apiResource = "Route::apiResource('$pluralModelName', {$namespace}\\{$modelNameController}::class);";

        if (!Str::contains($existingRouteContent, $apiResource)) {
            File::append($routeFilePath, "\n$apiResource");
            $this->command->info("API resource route for '$modelName' added successfully.");
        } else {
            $this->command->info("API resource route for '$modelName' already exists.");
        }
    }

    public function generateService($modelName, $directory): void
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

        $serviceContent = file_get_contents($serviceStubPath);

        $serviceContent = str_replace('{ModelName}', $modelName, $serviceContent);

        $serviceClassName = "{$modelName}Service";

        $servicePath = $directory
            ? $directory . '/' . $serviceClassName . '.php'
            : $servicesDirectory . '/' . $serviceClassName . '.php';

        $targetDirectory = dirname($servicePath);
        if (!is_dir($targetDirectory)) {
            mkdir($targetDirectory, 0755, true);
        }

        file_put_contents($servicePath, $serviceContent);

        $this->command->info("Service generated successfully: {$servicePath}");
    }

    public function generateRepository($modelName, $directory): void
    {
        $repositoryDirectory = app_path('Repositories/Eloquent/');
        if (!is_dir($repositoryDirectory)) {
            mkdir($repositoryDirectory, 0755, true);
        }

        $repositoryStubPath = resource_path('stubs/repository.stub');

        if (!file_exists($repositoryStubPath)) {
            $this->command->error("Service stub file not found: {$repositoryStubPath}");
            return;
        }

        $repositoryContent = file_get_contents($repositoryStubPath);

        $repositoryContent = str_replace('{ModelName}', $modelName, $repositoryContent);

        $repositoryClassName = "{$modelName}Repository";

        $repositoryPath = $directory
            ? $directory . '/' . $repositoryClassName . '.php'
            : $repositoryDirectory . '/' . $repositoryClassName . '.php';

        $targetDirectory = dirname($repositoryPath);
        if (!is_dir($targetDirectory)) {
            mkdir($targetDirectory, 0755, true);
        }

        file_put_contents($repositoryPath, $repositoryContent);

        $this->command->info("Repository generated successfully: {$repositoryPath}");
    }

    public function generateInterface($modelName, $directory): void
    {
        $interfaceDirectory = app_path('Repositories/Contracts/');
        if (!is_dir($interfaceDirectory)) {
            mkdir($interfaceDirectory, 0755, true);
        }

        $interfaceStubPath = resource_path('stubs/interface.stub');

        if (!file_exists($interfaceStubPath)) {
            $this->command->error("Service stub file not found: {$interfaceStubPath}");
            return;
        }

        $interfaceContent = file_get_contents($interfaceStubPath);

        $interfaceContent = str_replace('{ModelName}', $modelName, $interfaceContent);

        $interfaceClassName = "{$modelName}RepositoryInterface";

        $interfacePath = $directory
            ? $directory . '/' . $interfaceClassName . '.php'
            : $interfaceDirectory . '/' . $interfaceClassName . '.php';

        $targetDirectory = dirname($interfacePath);
        if (!is_dir($targetDirectory)) {
            mkdir($targetDirectory, 0755, true);
        }

        file_put_contents($interfacePath, $interfaceContent);

        $this->command->info("Interface generated successfully: {$interfacePath}");
    }

    public function includeDemoControllerContent($modelName, $directory): void
    {
        $modelKeyName = Str::camel(class_basename($modelName));

        $controllerName = "{$modelName}Controller";

        $demoControllerStubPath = resource_path('stubs/controller.stub');

        if (!file_exists($demoControllerStubPath)) {
            $this->command->error("Demo Controller stub file not found: {$demoControllerStubPath}");
            return;
        }

        $demoControllerContent = file_get_contents($demoControllerStubPath);
        $placeholders = ['{ModelName}', '{ModelKeyName}', '{ControllerName}'];
        $replacements = [$modelName, $modelKeyName, $controllerName];
        $demoControllerContent = str_replace($placeholders, $replacements, $demoControllerContent);

        $controllerPath = $directory
            ? $directory . '/Http/Controllers/' . $controllerName . '.php'
            : app_path('Http/Controllers/') . $controllerName . '.php';

        $targetDirectory = dirname($controllerPath);
        if (!is_dir($targetDirectory)) {
            mkdir($targetDirectory, 0755, true);
        }

        file_put_contents($controllerPath, $demoControllerContent);

        $this->command->info("Demo Controller generated successfully: {$controllerPath}");
    }
}
