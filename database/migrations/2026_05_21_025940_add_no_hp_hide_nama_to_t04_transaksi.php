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
        Schema::table('t04_transaksi', function (Blueprint $table) {
            if (!Schema::hasColumn('t04_transaksi', 'no_hp')) {
                $table->string('no_hp')->nullable()->after('nama');
            }
            if (!Schema::hasColumn('t04_transaksi', 'hide_nama')) {
                $table->smallInteger('hide_nama')->nullable()->default(0)->after('pesan_doa');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t04_transaksi', function (Blueprint $table) {
            $table->dropColumn(['no_hp', 'hide_nama']);
        });
    }
};
