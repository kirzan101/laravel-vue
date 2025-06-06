<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\UserGroup;
use App\Models\UserGroupPermission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userGroups = [
            [
                'name' => 'Admin',
                'code' => 'ADMIN',
                'description' => 'Administrator group',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'name' => 'Users',
                'code' => 'USERS',
                'description' => 'Users group',
                'created_by' => 1,
                'updated_by' => 1,
            ],
        ];

        foreach ($userGroups as $userGroup) {
            $createdUserGroup = UserGroup::updateOrCreate(
                ['code' => $userGroup['code']],
                $userGroup
            );

            $permissions = Permission::all();

            foreach ($permissions as $permission) {
                $exists = UserGroupPermission::where('user_group_id', $createdUserGroup->id)
                    ->where('permission_id', $permission->id)
                    ->exists();

                if (! $exists) {
                    UserGroupPermission::create([
                        'user_group_id' => $createdUserGroup->id,
                        'permission_id' => $permission->id,
                        'is_active' => $permission->is_active, // ✅ snake_case
                    ]);
                }
            }
        }
    }
}
