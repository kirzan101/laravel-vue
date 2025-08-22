<?php

namespace Database\Seeders;

use App\Helpers\Helper;
use App\Models\Profile;
use App\Models\ProfileUserGroup;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'username' => 'admin',
                'email' => 'admin@mail.com',
                'password' => bcrypt('admin'),
                'is_admin' => true,
                'status' => 'active',
                'first_name' => 'Admin',
                'last_name' => 'User',
                'nickname' => 'Admin',
                'type' => Helper::ACCOUNT_TYPE_ADMIN,
                'contact_numbers' => [
                    '0999999999'
                ],
                'created_by' => null,
                'updated_by' => null,
                'user_group_code' => Helper::USER_GROUP_CODE_ADMIN,
            ],
            [
                'username' => 'user1',
                'email' => 'user@mail.com',
                'password' => bcrypt('user1'),
                'is_admin' => false,
                'status' => 'active',
                'first_name' => 'User',
                'last_name' => 'Account',
                'nickname' => 'user',
                'type' => Helper::ACCOUNT_TYPE_USER,
                'contact_numbers' => [
                    '0999999991'
                ],
                'created_by' => null,
                'updated_by' => null,
                'user_group_code' => Helper::USER_GROUP_CODE_USER,
            ]
        ];

        foreach ($users as $user) {
            $createdUser = User::create([
                'username' => $user['username'],
                'email' => $user['email'],
                'password' => $user['password'],
                'is_admin' => $user['is_admin'],
                'status' => $user['status'],
            ]);

            $createdProfile = Profile::create([
                'user_id' => $createdUser->id,
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name'],
                'nickname' => $user['nickname'],
                'type' => $user['type'],
                'contact_numbers' => $user['contact_numbers'], //array of contact numbers
                'created_by' => $user['created_by'],
                'updated_by' => $user['updated_by'],
            ]);

            $userGroup = UserGroup::where('code', $user['user_group_code'])->first();

            // add user group to profile
            if ($userGroup) {
                ProfileUserGroup::create([
                    'profile_id' => $createdProfile->id,
                    'user_group_id' => $userGroup->id,
                ]);
            }
        }
    }
}
