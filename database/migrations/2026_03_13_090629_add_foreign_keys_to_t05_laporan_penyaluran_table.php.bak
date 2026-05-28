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
        Schema::table('t05_laporan_penyaluran', function (Blueprint $table) {
            $table->foreign(['id_program'], 't05_laporan_penyaluran_id_program_fkey')->references(['id_program'])->on('t03_program_wakaf')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t05_laporan_penyaluran', function (Blueprint $table) {
            $table->dropForeign('t05_laporan_penyaluran_id_program_fkey');
        });
    }
};
