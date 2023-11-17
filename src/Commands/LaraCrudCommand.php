<?php

namespace laracrud\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use laracrud\Services\LaraCrudService;

class LaraCrudCommand extends Command
{
    protected $signature = 'lara:crud';
    protected $description = 'Generate model, migration, request, validation, route, and service for CRUD';

    public function handle(LaraCrudService $crudService)
    {
        $crudService->setCommand($this);

        list($modelName, $directory, $fillableFields, $foreignIdFields) = $crudService->getInteractiveInputs();

        $pluralModelName = Str::plural(strtolower($modelName));

        $crudService->generateModel($modelName, $pluralModelName, $directory, $fillableFields, $foreignIdFields);
        $crudService->generateMigration($modelName, $pluralModelName, $directory, $fillableFields, $foreignIdFields);
        $crudService->generateRequest($modelName, $directory);
        $crudService->generateValidation("App\Http\Requests\\{$modelName}Request", $directory);
        $crudService->generateService($modelName, $directory);
        $crudService->generateRepository($modelName, $directory);
        $crudService->generateInterface($modelName, $directory);
        $crudService->includeDemoControllerContent($modelName, $directory);
        $crudService->appendRoute($modelName, $pluralModelName, $directory);

        $this->info('CRUD code generated successfully!');
    }
}
