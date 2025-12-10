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
        Schema::create('regions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->jsonb('polygon'); // Array of coordinates: [[lat, lng], [lat, lng], ...]
            $table->string('timezone')->default('Asia/Jakarta');
            $table->string('currency_code')->default('IDR');
            $table->boolean('is_active')->default(true);
            $table->uuid('parent_id')->nullable();
            $table->integer('level')->default(1);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('parent_id')->references('id')->on('regions');
            $table->index(['is_active']);
        });

        Schema::create('region_services', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('region_id');
            $table->string('service_name'); // 'food', 'ride', 'mart', 'send'
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('region_id')->references('id')->on('regions')->onDelete('cascade');
            $table->unique(['region_id', 'service_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('region_services');
        Schema::dropIfExists('regions');
    }
};
