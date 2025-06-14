<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeServiceCommand extends Command
{
    protected $signature = 'make:service {name}';
    protected $description = 'Generate a new service class and corresponding interface using stubs';

    public function handle(): void
    {
        $baseName = Str::studly($this->argument('name')); // e.g., "UserGroup"
        $className = "{$baseName}Service";                // e.g., "UserGroupService"
        $interfaceName = "{$baseName}Interface";          // e.g., "UserGroupInterface"
        $readableLabel = ucfirst(strtolower(preg_replace('/(?<!^)[A-Z]/', ' $0', $baseName)));
        $readableDescription = strtolower(strtolower(preg_replace('/(?<!^)[A-Z]/', ' $0', $baseName)));

        // Paths
        $servicePath = app_path("Services/{$className}.php");
        $interfacePath = app_path("Interfaces/{$interfaceName}.php");

        $serviceStubPath = base_path('stubs/service.stub');
        $interfaceStubPath = base_path('stubs/interface.stub');

        // Validate stub files
        if (!File::exists($serviceStubPath) || !File::exists($interfaceStubPath)) {
            $this->error('One or both stub files are missing in /stubs directory.');
            return;
        }

        // Prevent overwrite
        if (File::exists($servicePath)) {
            $this->error("Service already exists at: {$servicePath}");
            return;
        }
        if (File::exists($interfacePath)) {
            $this->error("Interface already exists at: {$interfacePath}");
            return;
        }

        // Generate service file
        $serviceStub = File::get($serviceStubPath);
        $serviceContent = str_replace(
            ['{{ namespace }}', '{{ class }}', '{{ base }}', '{{ interface }}', '{{ readable }}', '{{ description }}'],
            ['App\\Services', $className, $baseName, $interfaceName, $readableLabel, $readableDescription],
            $serviceStub
        );
        File::ensureDirectoryExists(app_path('Services'));
        File::put($servicePath, $serviceContent);

        // Generate interface file
        $interfaceStub = File::get($interfaceStubPath);
        $interfaceContent = str_replace(
            ['{{ namespace }}', '{{ interface }}', '{{ base }}', '{{ readable }}', '{{ description }}'],
            ['App\\Interfaces', $interfaceName, $baseName, $readableLabel, $readableDescription],
            $interfaceStub
        );
        File::ensureDirectoryExists(app_path('Interfaces'));
        File::put($interfacePath, $interfaceContent);

        // Success messages
        $this->components->info("Interface [app/Interfaces/{$interfaceName}.php] created successfully.");
        $this->components->info("Service [app/Services/{$className}.php] created successfully.");
    }
}
