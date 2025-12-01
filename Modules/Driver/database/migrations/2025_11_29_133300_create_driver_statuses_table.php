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
        Schema::create('driver_statuses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('profile_id');
            $table->enum('online_status', array_column(\Modules\Driver\Enums\OnlineStatusEnum::cases(), 'value'))->default('offline');
            $table->enum('operational_status', array_column(\Modules\Driver\Enums\OperationalStatusEnum::cases(), 'value'))->default('available');
            $table->enum('active_service', array_column(\App\Enums\ServiceTypeEnum::cases(), 'value'))->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->timestamp('last_updated_at')->useCurrent();
            $table->timestamps();

            $table->foreign('profile_id')->references('id')->on('profiles')->onDelete('cascade');
            $table->unique('profile_id');
            $table->index(['online_status', 'operational_status']);
            $table->index('active_service');
            $table->index('last_updated_at');
            $table->index(['latitude', 'longitude']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_statuses');
    }
};
