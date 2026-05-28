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
        Schema::create('t01_roles', function (Blueprint $table) {
            $table->increments('id_role');
            $table->string('nama_role', 20);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('edited_at')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t01_roles');
    }
};
