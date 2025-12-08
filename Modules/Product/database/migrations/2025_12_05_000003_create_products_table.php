<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Nwidart\Modules\Module;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('merchant_id');
            $table->uuid('category_id')->nullable();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->enum('type', array_column(\Modules\Product\Enums\ProductTypeEnum::cases(), 'value'));
            $table->string('barcode')->nullable();
            $table->string('sku')->nullable();
            $table->string('base_unit')->nullable();
            $table->decimal('price', 15, 2)->nullable();
            $table->boolean('has_variant')->default(false);
            $table->boolean('has_expired')->default(false);
            $table->jsonb('metadata')->nullable();
            $table->integer('version')->default(1);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['merchant_id', 'sku']);
            $table->unique(['merchant_id', 'barcode']);
            $table->index(['merchant_id']);
            $table->index(['category_id']);
            $table->index(['slug']);
            $table->index(['type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
