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
        Schema::create('merchant_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('merchant_id');

            // Delivery Settings
            $table->boolean('delivery_enabled')->default(true);
            $table->integer('delivery_radius_km')->default(5);
            $table->decimal('minimum_order_amount', 10, 2)->default(0);

            // Basic Settings (for future expansion)
            $table->boolean('auto_accept_orders')->default(true);
            $table->integer('preparation_time_minutes')->default(15);
            $table->boolean('notifications_enabled')->default(true);

            $table->timestamps();

            // Foreign key
            $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('cascade');

            // Indexes
            $table->index('merchant_id');
            $table->index(['delivery_enabled', 'delivery_radius_km']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('merchant_settings');
    }
};
