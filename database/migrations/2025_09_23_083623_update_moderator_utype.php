<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing moderator user to have utype = 'ADM'
        DB::table('users')
            ->where('email', 'moderator@test.com')
            ->update(['utype' => 'ADM']);
            
        // Update all users with admin, manager, or moderator roles to have utype = 'ADM'
        DB::table('users')
            ->whereIn('role', ['admin', 'manager', 'moderator'])
            ->update(['utype' => 'ADM']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert moderator user back to USR
        DB::table('users')
            ->where('email', 'moderator@test.com')
            ->update(['utype' => 'USR']);
    }
};