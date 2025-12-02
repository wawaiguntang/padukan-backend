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
        Schema::create('profiles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('user_id')->unique();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('avatar')->nullable();
            $table->enum('gender', array_column(\Modules\Customer\Enums\GenderEnum::cases(), 'value'))->default('other');
            $table->string('language')->default('id');
            $table->boolean('is_verified')->default(false);
            $table->enum('verification_status', array_column(\Modules\Customer\Enums\VerificationStatusEnum::cases(), 'value'))->default('pending');
            $table->timestamps();

            $table->index('user_id');
            $table->index(['is_verified', 'verification_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
