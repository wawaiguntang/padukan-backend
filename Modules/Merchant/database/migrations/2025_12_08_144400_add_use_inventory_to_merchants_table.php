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
        Schema::table('merchant_settings', function (Blueprint $table) {
            $table->boolean('use_inventory')->default(false)->after('notifications_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('merchant_settings', function (Blueprint $table) {
            $table->dropColumn('use_inventory');
        });
    }
};
