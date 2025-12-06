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
        Schema::create('product_service_details', function (Blueprint $table) {
            $table->uuid('service_id')->primary();
            $table->uuid('product_id');
            $table->uuid('variant_id')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->integer('staff_required')->nullable();
            $table->integer('min_participants')->nullable();
            $table->integer('max_participants')->nullable();
            $table->jsonb('optional_extras')->nullable();
            $table->jsonb('metadata')->nullable();
            $table->timestamps();

            $table->index(['product_id']);
            $table->index(['variant_id']);

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('variant_id')->references('id')->on('product_variants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_service_details');
    }
};
