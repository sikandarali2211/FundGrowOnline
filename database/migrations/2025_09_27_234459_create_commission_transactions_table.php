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
        Schema::create('commission_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // User who gets commission
            $table->unsignedBigInteger('plan_selection_id'); // Which plan purchase triggered this
            $table->decimal('total_commission', 15, 2); // Total commission amount
            $table->decimal('pool_commission', 15, 2); // 60% - Pool commission
            $table->decimal('profit_commission', 15, 2); // 30% - Profit commission
            $table->decimal('global_pool_commission', 15, 2); // 10% - Global pool
            $table->enum('commission_type', ['second_plan', 'third_plan', 'fourth_plan', 'fifth_plan', 'sixth_plan', 'seventh_plan', 'eighth_plan', 'ninth_plan', 'tenth_plan', 'eleventh_plan', 'twelfth_plan', 'thirteenth_plan', 'fourteenth_plan', 'fifteenth_plan', 'referral_chain']); // Type of commission
            $table->string('description')->nullable(); // Description of the commission
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('plan_selection_id')->references('id')->on('plan_selections')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commission_transactions');
    }
};
