<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('profile_picture')->nullable();  // Add profile_picture field
            $table->string('phone_number')->nullable();    // Add phone_number field
            $table->string('country')->nullable();         // Add country field
            $table->string('full_name')->nullable();       // Add full_name field
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('profile_picture');  // Drop profile_picture field
            $table->dropColumn('phone_number');    // Drop phone_number field
            $table->dropColumn('country');         // Drop country field
            $table->dropColumn('full_name');       // Drop full_name field
        });
    }
};
