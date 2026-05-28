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
            if (!Schema::hasColumn('t05_laporan_penyaluran', 'penerima_manfaat')) {
                $table->integer('penerima_manfaat')->nullable()->after('dana_disalurkan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t05_laporan_penyaluran', function (Blueprint $table) {
            $table->dropColumn('penerima_manfaat');
        });
    }
};
