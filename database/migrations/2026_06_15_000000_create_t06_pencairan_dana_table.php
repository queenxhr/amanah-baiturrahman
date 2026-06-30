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
        Schema::create('t06_pencairan_dana', function (Blueprint $table) {
            $table->increments('id_pencairan');
            $table->integer('id_program');
            $table->integer('id_user');
            $table->decimal('jumlah_dana', 15, 2);
            $table->text('keterangan')->nullable();
            $table->smallInteger('status_pencairan')->default(0); // 0=Pending, 1=Approved, 2=Rejected
            $table->timestamps();

            // Foreign keys
            $table->foreign('id_program')
                  ->references('id_program')
                  ->on('t03_program_wakaf')
                  ->onDelete('cascade');

            $table->foreign('id_user')
                  ->references('id_user')
                  ->on('t02_users')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t06_pencairan_dana');
    }
};
