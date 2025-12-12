<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promotion_targets', function (Blueprint $table) {
            /**
             * @id VARCHAR - ID unik target (UUID)
             */
            $table->uuid('id')->primary();

            /**
             * @promotion_id VARCHAR - FK ke promotions (UUID)
             */
            $table->uuid('promotion_id');
            $table->foreign('promotion_id')->references('id')->on('promotions')->onDelete('cascade');

            /**
             * @target_type VARCHAR - Tipe target: product, category, merchant, user, dsb
             */
            $table->string('target_type');

            /**
             * @target_id VARCHAR - ID target eksternal
             */
            $table->string('target_id');

            /**
             * @operator ENUM - include / exclude
             */
            $table->enum('operator', ['include', 'exclude'])->default('include');

            /**
             * @created_at TIMESTAMP - Waktu dibuat
             * @updated_at TIMESTAMP - Waktu diupdate
             */
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('promotion_targets');
    }
};
