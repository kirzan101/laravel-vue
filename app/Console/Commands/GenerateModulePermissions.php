<?php

namespace App\Console\Commands;

use App\Models\Permission;
use App\Models\UserGroup;
use App\Models\UserGroupPermission;
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

    protected $description = 'Generate module CRUD permissions and assign them to all user groups';

    public function handle()
    {
        DB::transaction(function () {
            $originalModelName = $this->argument('model');
            $singularName = Str::snake($originalModelName);

            // Try to pluralize the word
            $pluralName = Str::plural($singularName);

            // Fallback if pluralization didn’t change anything (likely no plural form)
            $modelName = $pluralName !== $singularName ? $pluralName : $singularName;

            $types = [
                'create' => $this->option('create') ?? true,
                'view'   => $this->option('view') ?? true,
                'update' => $this->option('update') ?? true,
                'delete' => $this->option('delete') ?? false,
            ];

            $createdPermissions = [];

            foreach ($types as $type => $isActive) {
                $permission = Permission::firstOrCreate(
                    ['module' => $modelName, 'type' => $type],
                    ['is_active' => $isActive]
                );

                $createdPermissions[] = $permission->id;
            }

            foreach (UserGroup::all() as $userGroup) {
                foreach ($createdPermissions as $permissionId) {
                    UserGroupPermission::firstOrCreate(
                        [
                            'user_group_id' => $userGroup->id,
                            'permission_id' => $permissionId,
                        ],
                        [
                            'is_active' => false, // default to inactive, but can be updated later
                        ]
                    );
                }
            }
        });

        $this->info('Permissions and user group links generated successfully.');
    }
}
