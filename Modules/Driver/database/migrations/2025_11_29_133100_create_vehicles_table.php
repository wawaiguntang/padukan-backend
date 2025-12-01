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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('driver_profile_id');
            $table->enum('type', array_column(\Modules\Driver\Enums\VehicleTypeEnum::cases(), 'value'));
            $table->string('brand');
            $table->string('model');
            $table->integer('year');
            $table->string('color');
            $table->string('license_plate');
            $table->boolean('is_verified')->default(false);
            $table->enum('verification_status', array_column(\Modules\Driver\Enums\VerificationStatusEnum::cases(), 'value'))->default('pending');
            $table->timestamps();

            $table->foreign('driver_profile_id')->references('id')->on('profiles')->onDelete('cascade');
            $table->index(['driver_profile_id', 'type']);
            $table->index(['is_verified', 'verification_status']);
            $table->unique(['driver_profile_id', 'license_plate']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
