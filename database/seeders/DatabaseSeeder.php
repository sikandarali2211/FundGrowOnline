<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Yahan call karna hai
        $this->call(AdminUserSeeder::class);
    }
}
