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
        Schema::create('t03_program_wakaf', function (Blueprint $table) {
            $table->increments('id_program');
            $table->string('nama_program', 150);
            $table->text('deskripsi')->nullable();
            $table->decimal('target_dana', 15);
            $table->decimal('dana_terkumpul', 15)->nullable()->default(0);
            $table->smallInteger('status_program')->nullable()->default(1);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('edited_at')->nullable()->useCurrent();
            $table->date('due_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t03_program_wakaf');
    }
};
