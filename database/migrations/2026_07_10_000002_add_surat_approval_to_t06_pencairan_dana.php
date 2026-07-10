<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('t06_pencairan_dana', function (Blueprint $table) {
            $table->string('surat_approval')->nullable()->after('status_pencairan');
        });
    }

    public function down(): void
    {
        Schema::table('t06_pencairan_dana', function (Blueprint $table) {
            $table->dropColumn('surat_approval');
        });
    }
};
