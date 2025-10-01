<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('commission_transactions', function (Blueprint $table) {
            $table->enum('commission_type', ['second_plan', 'third_plan', 'fourth_plan', 'fifth_plan', 'referral_chain'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commission_transactions', function (Blueprint $table) {
            $table->enum('commission_type', ['second_plan', 'third_plan', 'fourth_plan', 'referral_chain'])->change();
        });
    }
};