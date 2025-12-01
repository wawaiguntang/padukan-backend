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
        Schema::create('merchant_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('merchant_profile_id');
            $table->enum('type', ['id_card', 'store']);
            $table->string('file_path');
            $table->string('file_name');
            $table->string('mime_type');
            $table->integer('file_size');
            $table->json('meta');
            $table->date('expiry_date')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->enum('verification_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamp('verified_at')->nullable();
            $table->string('verified_by')->nullable();
            $table->timestamps();

            $table->foreign('merchant_profile_id')->references('id')->on('merchant_profiles')->onDelete('cascade');
            $table->index('merchant_profile_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('merchant_documents');
    }
};
