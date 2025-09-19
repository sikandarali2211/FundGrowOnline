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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('plan_id')->nullable()->constrained('investment_plans')->onDelete('set null');
            $table->string('transaction_hash')->unique();
            $table->string('from_address');
            $table->string('to_address');
            $table->decimal('amount', 18, 8);
            $table->string('currency', 10)->default('BNB');
            $table->string('status')->default('pending'); // pending, confirmed, failed, expired
            $table->integer('block_number')->nullable();
            $table->integer('confirmations')->default(0);
            $table->text('admin_notes')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['status', 'created_at']);
            $table->index('transaction_hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
