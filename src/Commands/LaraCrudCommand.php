<?php

namespace laracrud\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\App;
use laracrud\Services\LaraCrudService;

class LaraCrudCommand extends Command
{
    protected $signature = 'lara:crud';
    protected $description = 'Generate model, migration, request, validation, route, and service for CRUD';


    public function handle(LaraCrudService $crudService)
    {
        $crudService->setCommand($this);

        list($modelName, $directory) = $crudService->getInteractiveInputs();
        $pluralModelName = Str::plural(strtolower($modelName));
        $crudService->generateModel($modelName, $directory);
        $crudService->generateMigration($modelName, $pluralModelName, $directory);
        $crudService->generateRequest($modelName, $directory);
        $crudService->generateValidation("App\Http\Requests\\{$modelName}Request", $directory);
        $crudService->generateService($modelName, $directory);
        $crudService->includeDemoControllerContent($modelName, $directory);

        $crudService->appendRoute($modelName, $pluralModelName, $directory);
        $crudService->accessBindModels($serviceProvider, $modelName);
        $crudService->info('CRUD code (excluding controller) generated successfully!');
    }
}
