<?php

// Simple script to create plan_selections table
require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

// Database configuration
$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => env('DB_PORT', '3306'),
    'database' => env('DB_DATABASE', 'fundgrowonline'),
    'username' => env('DB_USERNAME', 'root'),
    'password' => env('DB_PASSWORD', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'strict' => false,
    'engine' => null,
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

// Create the table
try {
    if (!Capsule::schema()->hasTable('plan_selections')) {
        Capsule::schema()->create('plan_selections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('plan_name');
            $table->decimal('plan_amount', 15, 2);
            $table->decimal('return_percentage', 5, 2);
            $table->integer('duration_days');
            $table->decimal('expected_return', 15, 2);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['status', 'created_at']);
        });
        
        echo "✅ plan_selections table created successfully!\n";
    } else {
        echo "ℹ️ plan_selections table already exists.\n";
    }
} catch (Exception $e) {
    echo "❌ Error creating table: " . $e->getMessage() . "\n";
}

