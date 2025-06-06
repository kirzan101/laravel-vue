<?php

namespace Database\Seeders;

use App\Models\Profile;
use App\Models\ProfileUserGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProfileUserGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $profiles = Profile::all();

        foreach ($profiles as $profile) {
            ProfileUserGroup::create([
                'profile_id' => $profile->id,
                'user_group_id' => $profile->id, // replace with actual user group ID if needed
            ]);
        }
    }
}
