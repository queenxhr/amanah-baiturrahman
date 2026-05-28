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
        Schema::table('t03_program_wakaf', function (Blueprint $table) {
            $table->string('gambar_thumbnail')->nullable();
        });

        Schema::table('t05_laporan_penyaluran', function (Blueprint $table) {
            $table->string('gambar_laporan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t03_program_wakaf', function (Blueprint $table) {
            $table->dropColumn('gambar_thumbnail');
        });

        Schema::table('t05_laporan_penyaluran', function (Blueprint $table) {
            $table->dropColumn('gambar_laporan');
        });
    }
};
