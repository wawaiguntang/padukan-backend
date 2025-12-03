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
        Schema::create('documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('documentable_id');
            $table->string('documentable_type');
            $table->enum('type', array_column(\Modules\Merchant\Enums\DocumentTypeEnum::cases(), 'value'));
            $table->string('file_path');
            $table->string('file_name');
            $table->string('mime_type');
            $table->integer('file_size');
            $table->json('meta');
            $table->date('expiry_date')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->enum('verification_status', array_column(\Modules\Merchant\Enums\VerificationStatusEnum::cases(), 'value'))->default('pending');
            $table->timestamp('verified_at')->nullable();
            $table->string('verified_by')->nullable();
            $table->timestamps();

            $table->index(['documentable_id', 'documentable_type']);
            $table->index(['type', 'verification_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
