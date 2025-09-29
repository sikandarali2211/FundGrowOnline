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
        Schema::create('global_pools', function (Blueprint $table) {
            $table->id();
            $table->decimal('total_amount', 15, 2)->default(0); // Total amount in global pool
            $table->integer('transaction_count')->default(0); // Number of transactions contributing
            $table->decimal('last_contribution', 15, 2)->default(0); // Last contribution amount
            $table->timestamp('last_updated')->nullable(); // Last update timestamp
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('global_pools');
    }
};
