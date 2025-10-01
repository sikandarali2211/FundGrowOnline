<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('commission_transactions', function (Blueprint $table) {
            $table->enum('commission_type', ['second_plan', 'third_plan', 'fourth_plan', 'fifth_plan', 'sixth_plan', 'seventh_plan', 'eighth_plan', 'ninth_plan', 'tenth_plan', 'eleventh_plan', 'twelfth_plan', 'thirteenth_plan', 'fourteenth_plan', 'fifteenth_plan', 'referral_chain'])->change();
        });
    }

    public function down(): void
    {
        Schema::table('commission_transactions', function (Blueprint $table) {
            $table->enum('commission_type', ['second_plan', 'third_plan', 'fourth_plan', 'fifth_plan', 'sixth_plan', 'seventh_plan', 'eighth_plan', 'ninth_plan', 'tenth_plan', 'eleventh_plan', 'twelfth_plan', 'thirteenth_plan', 'fourteenth_plan', 'referral_chain'])->change();
        });
    }
};
