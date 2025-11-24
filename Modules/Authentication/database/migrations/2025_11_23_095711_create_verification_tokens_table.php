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
        // Skip creating verification_tokens table if it already exists
        if (!Schema::hasTable('verification_tokens')) {
            Schema::create('verification_tokens', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
                $table->enum('type', ['email', 'phone']);
                $table->string('token');
                $table->boolean('is_used')->default(false);
                $table->dateTime('expires_at');
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
        if (Schema::hasTable('verification_tokens')) {
            Schema::dropIfExists('verification_tokens');
        }
    }
};
