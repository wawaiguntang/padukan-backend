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
        Schema::create('campaign_promotions', function (Blueprint $table) {
            /**
             * @id VARCHAR - ID unik pivot record (UUID)
             */
            $table->uuid('id')->primary();

            /**
             * @campaign_id VARCHAR - FK ke campaigns (UUID)
             */
            $table->uuid('campaign_id');
            $table->foreign('campaign_id')->references('id')->on('campaigns')->onDelete('cascade');

            /**
             * @promotion_id VARCHAR - FK ke promotions (UUID)
             */
            $table->uuid('promotion_id');
            $table->foreign('promotion_id')->references('id')->on('promotions')->onDelete('cascade');

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
        Schema::dropIfExists('campaign_promotions');
    }
};
