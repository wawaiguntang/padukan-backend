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
        Schema::create('tax_rules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->decimal('rate', 5, 2); // Percentage (e.g. 11.00)
            $table->enum('type', ['percentage', 'fixed'])->default('percentage');
            $table->uuid('region_id')->nullable(); // Nullable for global tax
            $table->uuid('category_id')->nullable(); // Nullable for all categories
            $table->uuid('tax_group_id')->nullable(); // For grouping products (e.g. 'Food', 'Service')
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0); // Higher priority applies first or overrides
            $table->timestamps();
            $table->softDeletes();

            $table->index(['region_id']);
            $table->index(['category_id']);
            $table->index(['tax_group_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_rules');
    }
};
