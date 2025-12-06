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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('product_id');
            $table->string('name');
            $table->string('sku')->nullable();
            $table->string('barcode')->nullable();
            $table->jsonb('attribute_master_ids')->nullable();
            $table->jsonb('attribute_custom_ids')->nullable();
            $table->string('unit')->nullable();
            $table->uuid('conversion_id')->nullable();
            $table->decimal('price', 15, 2);
            $table->boolean('has_expired')->default(false);
            $table->jsonb('metadata')->nullable();
            $table->integer('version')->default(1);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['sku']);
            $table->unique(['barcode']);
            $table->index(['product_id']);
            $table->index(['conversion_id']);
            $table->index(['has_expired']);

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
