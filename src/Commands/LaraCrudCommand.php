<?php

namespace LaraCrud\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class LaraCrudCommand extends Command
{
    protected $signature = 'laracrud:generate {name}';
    protected $description = 'Generate a complete CRUD operation';

    public function handle()
    {
        $name = $this->argument('name');

        // Create directories
        $this->makeDirectories($name);

        // Generate files
        $this->generateFiles($name);

        $this->info('CRUD generated successfully!');
    }

    protected function makeDirectories($name)
    {
        File::makeDirectory(app_path("Http/Controllers/{$name}"), 0755, true, true);
        File::makeDirectory(resource_path("views/{$name}"), 0755, true, true);
    }

    protected function generateFiles($name)
    {
        // Generate Controller
        $this->call('make:controller', [
            'name' => "{$name}Controller",
        ]);

        // Generate Model
        $this->call('make:model', [
            'name' => $name,
        ]);

        // Generate Views
        $this->generateViews($name);

        // Generate Validation Files
        $this->generateValidationFiles($name);

        // Generate Migration
        $this->call('make:migration', [
            'name' => "create_{$name}_table",
        ]);

        // Generate Routes
        $this->generateRoutes($name);
    }

    protected function generateViews($name)
    {
        // You can customize this based on your view structure
        File::copyDirectory(__DIR__.'/stubs/views', resource_path("views/{$name}"));
    }

    protected function generateValidationFiles($name)
    {
        // You can customize this based on your validation needs
        File::put(resource_path("lang/en/{$name}.php"), "<?php\n\nreturn [];");
    }

    protected function generateRoutes($name)
    {
        // You can customize this based on your route structure
        File::append(base_path('routes/web.php'), "\n// CRUD Routes for {$name}\n// Route::resource('{$name}', '{$name}Controller');\n");
    }
}
