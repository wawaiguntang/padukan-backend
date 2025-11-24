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
        // Skip creating users table if it already exists (from main app)
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('phone')->unique()->nullable();
                $table->string('email')->unique()->nullable();
                $table->string('password');
                $table->enum('status', ['pending', 'active', 'suspend'])->default('pending');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only drop if this migration created the table
        if (Schema::hasTable('users')) {
            Schema::dropIfExists('users');
        }
    }
};
