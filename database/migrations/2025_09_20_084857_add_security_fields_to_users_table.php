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
            $table->string('security_pin', 6)->nullable()->after('password');
            $table->string('otp_code', 6)->nullable()->after('security_pin');
            $table->timestamp('otp_expires_at')->nullable()->after('otp_code');
            $table->boolean('pin_setup_required')->default(true)->after('otp_expires_at');
            $table->timestamp('pin_setup_completed_at')->nullable()->after('pin_setup_required');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['security_pin', 'otp_code', 'otp_expires_at', 'pin_setup_required', 'pin_setup_completed_at']);
        });
    }
};
