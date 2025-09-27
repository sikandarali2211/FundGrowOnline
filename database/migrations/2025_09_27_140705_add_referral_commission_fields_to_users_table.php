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
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('referral_commission_balance', 15, 2)->default(0)->after('pool_wallet_amount');
            $table->decimal('referral_commission_pool', 15, 2)->default(0)->after('referral_commission_balance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['referral_commission_balance', 'referral_commission_pool']);
        });
    }
};
