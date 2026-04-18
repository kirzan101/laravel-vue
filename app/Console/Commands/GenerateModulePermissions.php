<?php

namespace App\Console\Commands;

use App\Helpers\Helper;
use App\Models\Module;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GenerateModulePermissions extends Command
{
    protected $signature = 'app:generate-module-permissions 
        {model : The name of the module} 
        {--create : Include create permission} 
        {--view : Include view permission} 
        {--update : Include update permission} 
        {--delete : Include delete permission}';

    protected $description = 'Generate module CRUD permissions and assign them to all roles';

    public function handle()
    {
        DB::transaction(function () {
            $originalModelName = $this->argument('model');

            $modelName = Helper::getModuleName($originalModelName);

            //if no arguments are provided, default to all permissions
            if (
                !$this->option('create') &&
                !$this->option('view') &&
                !$this->option('update') &&
                !$this->option('delete')
            ) {
                $types = [
                    'create' => true,
                    'view'   => true,
                    'update' => true,
                    'delete' => false, // default to false for delete permission
                ];
            } else {
                $types = [
                    'create' => $this->option('create'),
                    'view'   => $this->option('view'),
                    'update' => $this->option('update'),
                    'delete' => $this->option('delete'),
                ];
            }

            $createdPermissions = [];

            foreach ($types as $type => $isActive) {
                $permission = Permission::firstOrCreate(
                    ['module' => $modelName, 'type' => $type],
                    ['is_active' => $isActive]
                );

                $createdPermissions[] = $permission->id;
            }

            $roles = Role::all();
            foreach ($roles as $role) {
                foreach ($createdPermissions as $permissionId) {
                    $role->rolePermissions()->firstOrCreate(
                        [
                            'permission_id' => $permissionId,
                            'role_id' => $role->id,
                        ],
                        ['is_active' => false] // default to inactive, but can be updated later
                    );
                }
            }

            // Create the module entry
            Module::create([
                'name' => $modelName,
                'icon' => 'mdi-home',
                'route' => '/' . Str::kebab($modelName),
                'category' => Helper::MODULE_CATEGORY_SYSTEM,
            ]);
        });

        $this->info('Permissions and roles links generated successfully.');
    }
}
