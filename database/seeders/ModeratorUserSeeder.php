<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class ModeratorUserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Test Moderator',
            'email' => 'moderator@test.com',
            'password' => bcrypt('password'),
            'role' => 'moderator',
            'utype' => 'ADM',
            'phone' => '1234567890',
            'referral' => '',
            'email_verified_at' => now(),
        ]);
        
        echo "Moderator user created successfully!\n";
    }
}