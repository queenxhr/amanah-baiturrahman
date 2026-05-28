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
        Schema::table('t02_users', function (Blueprint $table) {
            $table->foreign(['id_role'], 't02_users_id_role_fkey')->references(['id_role'])->on('t01_roles')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t02_users', function (Blueprint $table) {
            $table->dropForeign('t02_users_id_role_fkey');
        });
    }
};
