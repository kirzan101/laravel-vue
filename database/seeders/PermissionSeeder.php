<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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
                'types' => ['create', 'view', 'update'],
            ],
            [
                'module' => 'user_groups',
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
        }
    }
}
