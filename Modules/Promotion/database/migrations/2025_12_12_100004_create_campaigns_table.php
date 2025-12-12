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
        Schema::create('campaigns', function (Blueprint $table) {
            /**
             * @id VARCHAR - ID campaign unik (UUID)
             */
            $table->uuid('id')->primary();

            /**
             * @name VARCHAR - Nama campaign
             */
            $table->string('name');

            /**
             * @description TEXT - Deskripsi campaign
             */
            $table->text('description')->nullable();

            /**
             * @banner_image VARCHAR - Gambar/banner campaign
             */
            $table->string('banner_image')->nullable();

            /**
             * @start_at TIMESTAMP - Mulai campaign
             */
            $table->timestamp('start_at')->nullable();

            /**
             * @end_at TIMESTAMP - Akhir campaign
             */
            $table->timestamp('end_at')->nullable();

            /**
             * @status ENUM - draft, active, expired
             */
            $table->enum('status', ['draft', 'active', 'expired'])->default('draft');

            /**
             * @metadata JSON - Data tambahan fleksibel: event type, tags, dsb
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
        Schema::dropIfExists('campaigns');
    }
};
