<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class MakeApiController extends Command
{
    protected $signature = 'make:api-controller {name : The name of the controller} {--model= : Optional model binding}';

    protected $description = 'Create a new controller in the API folder with API methods';

    public function handle()
    {
        $name = $this->argument('name');
        $model = $this->option('model');

        // Ensure it goes into API namespace
        // $controllerPath = "API/" . $name;
        $controllerPath = sprintf('API/%sApiController', $name);

        // Build the Artisan command
        $command = [
            'name' => $controllerPath,
            '--api' => true,
        ];

        if ($model) {
            $command['--model'] = $model;
        }

        // Call the artisan make:controller command
        Artisan::call('make:controller', $command);

        $this->components->info("API controller [App\\Http\\Controllers\\API\\{$name}.php] created successfully.");
    }
}
