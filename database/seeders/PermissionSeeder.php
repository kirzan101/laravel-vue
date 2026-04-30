<?php

namespace Database\Seeders;

use App\Helpers\Helper;
use App\Models\Module;
use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            [
                'module' => 'profiles',
                'icon' => 'mdi-account',
                'types' => ['create', 'view', 'update'],
            ],
            [
                'module' => 'user_groups',
                'icon' => 'mdi-account-group',
                'types' => ['create', 'view', 'update'],
            ],
            [
                'module' => 'roles',
                'icon' => 'mdi-shield-account',
                'types' => ['create', 'view', 'update'],
            ],
            [
                'module' => 'modules',
                'icon' => 'mdi-view-dashboard',
                'types' => ['create', 'view', 'update'],
            ],
        ];

        $accessTypes = ['create', 'view', 'update', 'delete'];

        foreach ($permissions as $permission) {
            foreach ($accessTypes as $type) {
                $exists = Permission::where('module', $permission['module'])
                    ->where('type', $type)
                    ->exists();

                if ($exists) {
                    continue;
                }

                Permission::create([
                    'module' => $permission['module'],
                    'type' => $type,
                    'is_active' => in_array($type, $permission['types']),
                ]);
            }

            // create module
            Module::create([
                'name' => Str::title(str_replace('_', ' ', $permission['module'])),
                'icon' => $permission['icon'],
                'category' => Helper::MODULE_CATEGORY_SYSTEM,
                'route' => '/' . str_replace('_', '-', $permission['module'])
            ]);
        }
    }
}
