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
        Schema::create('unit_conversions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('from_unit');
            $table->string('to_unit');
            $table->decimal('multiply_factor', 15, 6);
            $table->jsonb('metadata')->nullable();
            $table->uuid('parent_conversion_id')->nullable();
            $table->timestamps();

            $table->index(['from_unit']);
            $table->index(['to_unit']);
            $table->index(['parent_conversion_id']);

            $table->foreign('parent_conversion_id')->references('id')->on('unit_conversions')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_conversions');
    }
};
