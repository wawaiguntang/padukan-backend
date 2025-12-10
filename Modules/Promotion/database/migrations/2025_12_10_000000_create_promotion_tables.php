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
        Schema::create('promotions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('creator_type', ['merchant', 'provider'])->default('merchant');
            $table->uuid('merchant_id')->nullable(); // Required if creator_type is merchant
            $table->string('image')->nullable();
            $table->dateTime('start_date');
            $table->dateTime('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('usage_limit')->nullable(); // Global limit
            $table->integer('usage_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['merchant_id']);
            $table->index(['start_date', 'end_date']);
            $table->index(['creator_type']);
        });

        Schema::create('promotion_rules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('promotion_id');
            $table->string('rule_type'); // min_order, specific_payment, specific_region, specific_service, customer_segment
            $table->string('operator')->default('='); // =, >, <, in
            $table->jsonb('value'); // The criteria value (e.g. 50000 for min_order, ['ID-JKT'] for region)
            $table->timestamps();

            $table->foreign('promotion_id')->references('id')->on('promotions')->onDelete('cascade');
        });

        Schema::create('promotion_benefits', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('promotion_id');
            $table->string('benefit_type'); // percentage_discount, fixed_discount, delivery_discount, free_item
            $table->decimal('value', 15, 2); // The discount amount or percentage
            $table->decimal('max_discount_amount', 15, 2)->nullable(); // Cap for percentage discount
            $table->jsonb('configuration')->nullable(); // Additional config (e.g. max_delivery_distance)
            $table->timestamps();

            $table->foreign('promotion_id')->references('id')->on('promotions')->onDelete('cascade');
        });

        Schema::create('coupons', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('promotion_id');
            $table->string('code')->unique();
            $table->integer('usage_limit')->nullable();
            $table->integer('usage_count')->default(0);
            $table->timestamps();

            $table->foreign('promotion_id')->references('id')->on('promotions')->onDelete('cascade');
            $table->index(['code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('promotion_benefits');
        Schema::dropIfExists('promotion_rules');
        Schema::dropIfExists('promotions');
    }
};
