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
        Schema::create('policies', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('resource')->nullable(); // Resource/entity this policy governs
            $table->json('actions')->nullable(); // Array of actions: ['read', 'write', 'delete', 'execute']
            $table->string('scope')->default('default'); // Authentication scope/context
            $table->string('group')->default('default'); // Policy group for ordering
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0); // Policy priority/order within group
            $table->json('conditions')->nullable(); // Complex conditions/rules as JSON
            $table->string('module')->nullable(); // Module this policy belongs to
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('policies');
    }
};
