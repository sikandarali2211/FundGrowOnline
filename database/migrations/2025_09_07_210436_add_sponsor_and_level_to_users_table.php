<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $t) {
            if (!Schema::hasColumn('users', 'sponsor_id')) {
                $t->foreignId('sponsor_id')->nullable()->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('users', 'level')) {
                $t->unsignedInteger('level')->default(1);
            }
            if (!Schema::hasColumn('users', 'referral_code')) {
                $t->string('referral_code', 50)->unique();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $t) {
            if (Schema::hasColumn('users', 'sponsor_id')) {
                $t->dropConstrainedForeignId('sponsor_id');
            }
            if (Schema::hasColumn('users', 'level')) {
                $t->dropColumn('level');
            }
            // DO NOT drop referral_code if you already use it elsewhere.
        });
    }
};
