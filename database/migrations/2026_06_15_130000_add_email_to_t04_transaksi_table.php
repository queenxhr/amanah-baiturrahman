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
            if (!Schema::hasColumn('t04_transaksi', 'email')) {
                $table->string('email')->nullable()->after('no_hp');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t04_transaksi', function (Blueprint $table) {
            if (Schema::hasColumn('t04_transaksi', 'email')) {
                $table->dropColumn('email');
            }
        });
    }
};
