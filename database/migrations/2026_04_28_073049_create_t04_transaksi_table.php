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
        Schema::create('t04_transaksi', function (Blueprint $table) {
            $table->increments('id_transaksi');
            $table->integer('id_user')->nullable();
            $table->string('nama')->nullable();
            $table->string('pesan_doa')->nullable();
            $table->integer('id_program')->nullable();
            $table->decimal('nominal', 15);
            $table->string('bukti_pembayaran')->nullable();
            $table->smallInteger('status_pembayaran')->nullable()->default(0);
            $table->string('kode_referensi', 100)->nullable()->unique('t04_transaksi_kode_referensi_key');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('edited_at')->nullable()->useCurrent();
            $table->string('no_hp')->nullable();
            $table->smallInteger('hide_nama')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t04_transaksi');
    }
};
