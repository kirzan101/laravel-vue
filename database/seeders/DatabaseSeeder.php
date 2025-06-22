<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        $this->call([
            PermissionSeeder::class,
            UserGroupSeeder::class,
            ProfileSeeder::class,
        ]);

        // Uncomment the line below to generate module permissions
        // $this->call(GenerateModulePermissions::class, ['model' => 'ExampleModel']);
    }
}
