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
        // 1. MASTER TAX DEFINITIONS (Dynamic Owner Support)
        Schema::create('taxes', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Dynamic polymorphic owner (any entity type can own taxes)
            $table->string('owner_type')->nullable()->comment('Any entity type: system, organization, merchant, franchise, etc.');
            $table->uuid('owner_id')->nullable()->comment('UUID of the owner entity');

            $table->string('name'); // e.g., "PPN", "Service Charge", "PB1"
            $table->string('slug')->unique(); // e.g., "ppn", "service-charge", "pb1"
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['owner_type', 'owner_id']);
            $table->index(['owner_type', 'is_active']);
        });

        // 2. TAX GROUPS (Dynamic Owner Support)
        Schema::create('tax_groups', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Dynamic polymorphic owner
            $table->string('owner_type')->nullable()->comment('Any entity type that owns this tax group');
            $table->uuid('owner_id')->nullable()->comment('UUID of the owner entity');

            $table->string('name'); // e.g., "National Taxes", "Restaurant Fees", "Branch Taxes"
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['owner_type', 'owner_id']);
            $table->index(['owner_type', 'is_active']);
        });

        // 3. TAX RATES (Belongs to Tax Groups)
        Schema::create('tax_rates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tax_group_id');
            $table->uuid('tax_id'); // Reference to master tax definition

            $table->decimal('rate', 8, 4); // Support both percentage (12.0000) and fixed amounts (5000.0000)
            $table->enum('type', ['percentage', 'fixed'])->default('percentage');
            $table->boolean('is_inclusive')->default(false)->comment('Tax included in price or added on top');

            // Calculation priority and logic
            $table->integer('priority')->default(0)->comment('Lower number = calculated first');
            $table->string('based_on')->nullable()->comment('subtotal, total_after_previous_tax, etc.');

            // Validity constraints
            $table->dateTime('valid_from')->nullable();
            $table->dateTime('valid_until')->nullable();
            $table->decimal('min_price', 15, 2)->nullable()->comment('Minimum price for this rate to apply');
            $table->decimal('max_price', 15, 2)->nullable()->comment('Maximum price for this rate to apply');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('tax_group_id')->references('id')->on('tax_groups')->onDelete('cascade');
            $table->foreign('tax_id')->references('id')->on('taxes')->onDelete('cascade');
            $table->index(['tax_group_id', 'valid_from', 'valid_until']);
            $table->index(['tax_group_id', 'priority']);
        });

        // 4. DYNAMIC TAX ASSIGNMENTS (Polymorphic - 100% Dynamic)
        Schema::create('tax_assignments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tax_group_id');

            // Polymorphic assignable entities - ANY entity type can be assigned
            $table->string('assignable_type')->comment('Entity type: region, category, product, branch, franchise, outlet, store, etc.');
            $table->uuid('assignable_id')->comment('UUID of the assignable entity');

            $table->timestamps();

            $table->foreign('tax_group_id')->references('id')->on('tax_groups')->onDelete('cascade');
            $table->index(['tax_group_id']);
            $table->index(['assignable_type', 'assignable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_assignments');
        Schema::dropIfExists('tax_rates');
        Schema::dropIfExists('tax_groups');
        Schema::dropIfExists('taxes');
    }
};
