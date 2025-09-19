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
        Schema::table('user_investments', function (Blueprint $table) {
            $table->foreignId('payment_transaction_id')->nullable()->constrained('payment_transactions')->onDelete('set null');
            $table->index('payment_transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_investments', function (Blueprint $table) {
            $table->dropForeign(['payment_transaction_id']);
            $table->dropIndex(['payment_transaction_id']);
            $table->dropColumn('payment_transaction_id');
        });
    }
};
