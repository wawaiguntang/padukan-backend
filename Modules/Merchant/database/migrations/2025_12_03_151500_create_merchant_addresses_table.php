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
            $table->string('merchant_id');

            $table->text('street');
            $table->string('city');
            $table->string('province');
            $table->string('country')->default('Indonesia');
            $table->string('postal_code');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            $table->timestamps();

            // Foreign key
            $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('cascade');

            // Indexes
            $table->index('merchant_id');
            $table->index(['latitude', 'longitude']);
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
