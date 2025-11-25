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
        // Roles table (RBAC foundation)
        Schema::create('roles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active']);
        });

        // User roles table (linking users to roles)
        Schema::create('user_roles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('user_id'); // From authentication module
            $table->uuid('role_id');
            $table->timestamps();

            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->unique(['user_id', 'role_id'], 'user_roles_unique');
            $table->index(['user_id']);
            $table->index(['role_id']);
        });

        // Permissions table (simple RBAC permissions)
        Schema::create('permissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('resource')->nullable(); // e.g., 'orders', 'users'
            $table->string('action')->nullable(); // e.g., 'create', 'read', 'update', 'delete'
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['resource', 'action']);
            $table->index(['is_active']);
        });

        // Role permissions table (RBAC assignments)
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('role_id');
            $table->uuid('permission_id');
            $table->timestamps();

            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
            $table->unique(['role_id', 'permission_id'], 'role_permissions_unique');
            $table->index(['role_id']);
            $table->index(['permission_id']);
        });

        // Policy settings table (JSON configuration for complex policies)
        Schema::create('policy_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('key')->unique(); // Policy identifier (e.g., 'driver_accept_order')
            $table->string('name'); // Human readable name
            $table->jsonb('settings'); // JSON configuration parameters
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['key']);
            $table->index(['is_active']);
            $table->index(['settings'], 'policy_settings_gin')->algorithm('gin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('policy_settings');
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('user_roles');
        Schema::dropIfExists('roles');
    }
};