<?php

namespace Database\Seeders;

use App\Models\Profile;
use App\Models\User;
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
                'type' => 'admin',
                'contact_numbers' => [
                    '0999999999'
                ],
                'created_by' => null,
                'updated_by' => null,
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
                'type' => 'user',
                'contact_numbers' => [
                    '0999999991'
                ],
                'created_by' => null,
                'updated_by' => null,
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

            Profile::create([
                'user_id' => $createdUser->id,
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name'],
                'nickname' => $user['nickname'],
                'type' => $user['type'],
                'contact_numbers' => $user['contact_numbers'], //array of contact numbers
                'created_by' => $user['created_by'],
                'updated_by' => $user['updated_by'],
            ]);
        }
    }
}
