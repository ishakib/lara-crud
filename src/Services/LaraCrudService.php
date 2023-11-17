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
        do {
            $modelName = $this->getModelName();
        } while (empty(trim($modelName)));

        // Get fillable fields from the user
        $fillableInput = $this->command->ask('Enter fillable fields (comma-separated, field:type)', 'name1:string, name2:string, date_of_birth:date....');
        $fillableFields = $this->parseFields($fillableInput);

        // Get foreign ID fields from the user
        $foreignIdInput = $this->command->ask('Enter foreign ID fields (comma-separated, or leave blank)', '');
        $foreignIdFields = $this->parseFields($foreignIdInput);

        $directory = null;

        return [$modelName, $directory, $fillableFields, $foreignIdFields];
    }

    private function getModelName(): string
    {
        do {
            $this->command->comment(
                $this->colorize('Note: Model name must be Singular and in PascalCase (Exp: ModelName).
            If you want to exit, type "q".', 'purple'));

            $modelName = $this->command->ask(
                $this->colorize('Enter model name...', 'cyan')
            );

            if (strtolower($modelName) === 'q') {
                $this->command->line($this->colorize('Exiting command...', 'yellow'));
                exit();
            }

        } while (empty(trim($modelName)));

        return $modelName;
    }

    private function colorize($text, $color): string
    {
        $colorCodes = [
            'black' => "\033[0;30m",
            'red' => "\033[0;31m",
            'green' => "\033[0;32m",
            'yellow' => "\033[0;33m",
            'blue' => "\033[0;34m",
            'purple' => "\033[1;35m",
            'cyan' => "\033[0;36m",
            'white' => "\033[0;37m",
        ];

        $reset = "\033[0m";

        return $colorCodes[$color] . $text . $reset;
    }

    public function parseFields($fields, $type = 'fillable')
    {
        $fieldsArray = [];

        foreach (explode(',', $fields) as $field) {
            $fieldParts = array_map('trim', explode(':', $field));
            $name = $fieldParts[0];

            if ($type === 'fillable') {
                $type = isset($fieldParts[1]) ? $fieldParts[1] : 'string';
            } elseif ($type === 'foreign_id') {
                $type = 'foreignId';
            }

            $fieldsArray[] = compact('name', 'type');
        }

        return $fieldsArray;
    }

    public function generateModel($modelName, $directory): void
    {
        // Determine the full path for the model
        $modelPath = app_path('Models');

        // If a directory is specified, append it to the model path
        if ($directory) {
            $modelPath .= '/' . $directory;
        }

        // Ensure the directory exists, and create it if not
        if (!file_exists($modelPath)) {
            mkdir($modelPath, 0755, true);
        }

        // Generate the model with the specified path
        Artisan::call('make:model', [
            'name' => "Models\\{$directory}\\{$modelName}",
        ]);
    }

    public function generateMigration($modelName, $pluralModelName, $directory, $fillableFields, $foreignIdFields): void
    {
        $pluralModelName = $this->convertToSnakeCase($modelName);
        $options = $directory ? ['--path' => "database/migrations/{$directory}"] : [];
        Artisan::call('make:migration', array_merge(['name' => "create_{$pluralModelName}_table", '--create' => $pluralModelName], $options));
    }

    public function convertToSnakeCase($input)
    {
        $snakeCase = preg_replace_callback('/([A-Z])/', function ($matches) {
            return '_' . strtolower($matches[1]);
        }, $input);

        $snakeCase = ltrim($snakeCase, '_');

        $snakeCase .= 's';

        return $snakeCase;
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
        $routeFilePath = base_path('routes/api.php');
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
