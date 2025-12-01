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
        Schema::create('merchant_addresses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('merchant_profile_id')->unique();
            $table->string('street');
            $table->string('city');
            $table->string('province');
            $table->string('postal_code');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->timestamps();

            $table->foreign('merchant_profile_id')->references('id')->on('merchant_profiles')->onDelete('cascade');
            $table->index('merchant_profile_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('merchant_addresses');
    }
};
