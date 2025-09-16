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
        Schema::table('investment_plans', function (Blueprint $table) {
            $table->decimal('min_amount', 15, 2)->default(0)->after('entry_amount');
            $table->decimal('max_amount', 15, 2)->default(999999)->after('min_amount');
            $table->integer('duration_days')->default(30)->after('return_percentage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('investment_plans', function (Blueprint $table) {
            $table->dropColumn(['min_amount', 'max_amount', 'duration_days']);
        });
    }
};

