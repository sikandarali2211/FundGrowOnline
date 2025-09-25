<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'balance_wallet')) {
                $table->decimal('balance_wallet', 24, 8)->default(0);
            }
            // speed for mapping by wallet
            if (!Schema::hasColumn('users', 'wallet_address')) {
                $table->string('wallet_address')->nullable()->index();
            } else {
                $table->index('wallet_address');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'balance_wallet')) {
                $table->dropColumn('balance_wallet');
            }
            try {
                $table->dropIndex(['wallet_address']);
            } catch (\Throwable $e) {
            }
        });
    }
};
