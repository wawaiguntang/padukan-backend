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
            $table->uuid('profile_id'); // Reference to profiles table

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
            $table->text('street');
            $table->string('city');
            $table->string('province');
            $table->string('country')->default('Indonesia');
            $table->string('postal_code');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            // Verification (separate from profile verification)
            $table->boolean('is_verified')->default(false);
            $table->enum('verification_status', array_column(\Modules\Merchant\Enums\VerificationStatusEnum::cases(), 'value'))->default('pending');

            // Regular operating hours (JSON structure)
            // Format: {"monday": {"open": "08:00", "close": "17:00", "is_open": true}, ...}
            $table->json('regular_hours');
            // Special schedules for holidays/events
            // Format: [{"date": "2024-12-25", "name": "Christmas", "is_open": false, "open_time": null, "close_time": null}, ...]
            $table->json('special_schedules')->nullable();
            $table->enum('status', array_column(\Modules\Merchant\Enums\MerchantStatusEnum::cases(), 'value'))->default('closed');


            $table->timestamps();

            // Foreign key
            $table->foreign('profile_id')->references('id')->on('profiles')->onDelete('cascade');

            // Indexes
            $table->index('profile_id');
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
