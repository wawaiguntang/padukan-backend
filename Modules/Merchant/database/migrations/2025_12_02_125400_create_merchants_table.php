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
        Schema::create('merchants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('profile_id'); // Reference to profiles table

            // Merchant Basic Info
            $table->string('business_name');
            $table->text('business_description')->nullable();
            $table->enum('business_category', array_column(\Modules\Merchant\Enums\BusinessCategoryEnum::cases(), 'value'))->nullable();
            $table->string('slug')->unique(); // URL-friendly identifier

            // Contact Information
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();

            // Location
            $table->text('address');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            // Verification (separate from profile verification)
            $table->boolean('is_verified')->default(false);
            $table->enum('verification_status', array_column(\Modules\Merchant\Enums\VerificationStatusEnum::cases(), 'value'))->default('pending');

            $table->timestamps();

            // Foreign key
            $table->foreign('profile_id')->references('id')->on('profiles')->onDelete('cascade');

            // Indexes
            $table->index('profile_id');
            $table->index(['is_active', 'status']);
            $table->index(['is_verified', 'verification_status']);
            $table->index('business_category');
            $table->index(['latitude', 'longitude']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('merchants');
    }
};
