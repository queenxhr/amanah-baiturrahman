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
        Schema::create('t05_laporan_penyaluran', function (Blueprint $table) {
            $table->increments('id_laporan');
            $table->integer('id_program')->nullable();
            $table->string('judul_laporan', 150);
            $table->text('keterangan')->nullable();
            $table->decimal('dana_disalurkan', 15);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('edited_at')->nullable()->useCurrent();
            $table->integer('penerima_manfaat')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t05_laporan_penyaluran');
    }
};
