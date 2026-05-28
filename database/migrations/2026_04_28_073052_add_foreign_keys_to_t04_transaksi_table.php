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
            $table->foreign(['id_program'], 't04_transaksi_id_program_fkey')->references(['id_program'])->on('t03_program_wakaf')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['id_user'], 't04_transaksi_id_user_fkey')->references(['id_user'])->on('t02_users')->onUpdate('no action')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t04_transaksi', function (Blueprint $table) {
            $table->dropForeign('t04_transaksi_id_program_fkey');
            $table->dropForeign('t04_transaksi_id_user_fkey');
        });
    }
};
