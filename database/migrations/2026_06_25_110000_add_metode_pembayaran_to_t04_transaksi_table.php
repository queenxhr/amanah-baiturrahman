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
            $table->string('metode_pembayaran', 20)->nullable()->default('QRIS');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t04_transaksi', function (Blueprint $table) {
            $table->dropColumn('metode_pembayaran');
        });
    }
};
