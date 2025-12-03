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
        Schema::create('merchant_schedules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('merchant_id'); // Reference to merchants table

            // Regular operating hours (JSON structure)
            // Format: {"monday": {"open": "08:00", "close": "17:00", "is_open": true}, ...}
            $table->json('regular_hours');

            // Special schedules for holidays/events
            // Format: [{"date": "2024-12-25", "name": "Christmas", "is_open": false, "open_time": null, "close_time": null}, ...]
            $table->json('special_schedules')->nullable();

            // Temporary closures
            // Format: [{"start_date": "2024-12-20", "end_date": "2024-12-26", "reason": "Holiday Season"}, ...]
            $table->json('temporary_closures')->nullable();

            // Current status
            $table->boolean('is_currently_open')->default(true);
            $table->timestamp('next_open_time')->nullable();
            $table->string('status_reason')->nullable(); // "regular_hours", "holiday", "temporary_closure", "manual_override"

            $table->timestamps();

            // Foreign key
            $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('cascade');

            // Indexes
            $table->index('merchant_id');
            $table->index('is_currently_open');
            $table->index('next_open_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('merchant_schedules');
    }
};
