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
        Schema::create('promotions', function (Blueprint $table) {
            /**
             * @id VARCHAR - ID unik promo, primary key (UUID)
             */
            $table->uuid('id')->primary();

            /**
             * @code VARCHAR - Kode promo / voucher (opsional)
             */
            $table->string('code')->nullable()->unique();

            /**
             * @name VARCHAR - Nama promo
             */
            $table->string('name');

            /**
             * @short_description TEXT - Ringkasan promo, tampil di UI
             */
            $table->text('short_description')->nullable();

            /**
             * @terms_conditions TEXT - Syarat & ketentuan promo secara lengkap
             */
            $table->text('terms_conditions')->nullable();

            /**
             * @banner_image VARCHAR - URL gambar/banner promo
             */
            $table->string('banner_image')->nullable();

            /**
             * @owner_type VARCHAR - Pemilik promo (admin, merchant, brand)
             */
            $table->string('owner_type');

            /**
             * @owner_id VARCHAR - ID pemilik promo
             */
            $table->string('owner_id');

            /**
             * @priority INT - Prioritas promo saat stackable
             */
            $table->integer('priority')->default(0);

            /**
             * @stackable BOOLEAN - Apakah promo bisa digabung dengan promo lain
             */
            $table->boolean('stackable')->default(false);

            /**
             * @start_at TIMESTAMP - Waktu mulai berlaku
             */
            $table->timestamp('start_at')->nullable();

            /**
             * @end_at TIMESTAMP - Waktu berakhir
             */
            $table->timestamp('end_at')->nullable();

            /**
             * @status ENUM - draft, active, expired, deleted
             */
            $table->enum('status', ['draft', 'active', 'expired', 'deleted'])->default('draft');

            /**
             * @rules_json JSON - Syarat / kondisi dinamis, format JSON
             */
            $table->json('rules_json')->nullable();

            /**
             * @actions_json JSON - Aksi promo, format JSON: discount, free item, cashback, dsb
             */
            $table->json('actions_json')->nullable();

            /**
             * @metadata JSON - Informasi tambahan fleksibel: tags, dsb
             */
            $table->json('metadata')->nullable();

            /**
             * @created_at TIMESTAMP - Waktu record dibuat
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
        Schema::dropIfExists('promotions');
    }
};
