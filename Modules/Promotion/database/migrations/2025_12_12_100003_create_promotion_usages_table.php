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
        Schema::create('promotion_usages', function (Blueprint $table) {
            /**
             * @id VARCHAR - ID unik usage (UUID)
             */
            $table->uuid('id')->primary();

            /**
             * @promotion_id VARCHAR - FK ke promotions (UUID)
             */
            $table->uuid('promotion_id');
            $table->foreign('promotion_id')->references('id')->on('promotions')->onDelete('cascade');

            /**
             * @usage_by_type VARCHAR - Siapa yang menggunakan: user, merchant, dsb
             */
            $table->string('usage_by_type');

            /**
             * @usage_by_id VARCHAR - ID pemakai
             */
            $table->string('usage_by_id');

            /**
             * @usage_on_type VARCHAR - Target entitas: order, cart, invoice, dsb
             */
            $table->string('usage_on_type');

            /**
             * @usage_on_id VARCHAR - ID target entitas
             */
            $table->string('usage_on_id');

            /**
             * @usage_at TIMESTAMP - Waktu digunakan
             */
            $table->timestamp('usage_at')->useCurrent();

            /**
             * @metadata JSON - Informasi tambahan: device, channel, cart_value, discount_applied
             */
            $table->json('metadata')->nullable();

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
        Schema::dropIfExists('promotion_usages');
    }
};
