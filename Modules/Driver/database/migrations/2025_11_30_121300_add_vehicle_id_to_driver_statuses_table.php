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
        Schema::table('driver_statuses', function (Blueprint $table) {
            $table->uuid('vehicle_id')->nullable()->after('active_service');
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('set null');
            $table->index('vehicle_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('driver_statuses', function (Blueprint $table) {
            $table->dropForeign(['vehicle_id']);
            $table->dropIndex(['vehicle_id']);
            $table->dropColumn('vehicle_id');
        });
    }
};
