<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('tx_hash')->unique();
            $table->string('from_address');
            $table->string('to_address');
            $table->decimal('amount', 20, 8);
            $table->string('token_address')->nullable(); // null for BNB, contract address for BEP20
            $table->string('token_symbol')->nullable();
            $table->string('status')->default('pending'); // pending, confirmed, failed
            $table->bigInteger('block_number')->nullable();
            $table->string('gas_used')->nullable();
            $table->json('transaction_data')->nullable(); // Store full transaction details
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index('tx_hash');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
