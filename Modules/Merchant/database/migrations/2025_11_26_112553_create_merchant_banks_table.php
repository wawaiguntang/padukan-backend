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
        Schema::create('merchant_banks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('merchant_profile_id');
            $table->uuid('bank_id');
            $table->string('account_number');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->foreign('merchant_profile_id')->references('id')->on('merchant_profiles')->onDelete('cascade');
            $table->foreign('bank_id')->references('id')->on('banks')->onDelete('cascade');
            $table->index(['merchant_profile_id', 'bank_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('merchant_banks');
    }
};
