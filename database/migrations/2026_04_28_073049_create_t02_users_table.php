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
        Schema::create('t02_users', function (Blueprint $table) {
            $table->increments('id_user');
            $table->integer('id_role')->nullable();
            $table->string('nama', 100);
            $table->string('email', 100)->unique('t02_users_email_key');
            $table->string('password');
            $table->string('no_hp', 15);
            $table->char('jenis_kelamin', 1)->nullable();
            $table->text('alamat')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('edited_at')->nullable()->useCurrent();
            $table->text('two_factor_secret')->nullable();
            $table->text('two_factor_recovery_codes')->nullable();
            $table->timestamp('two_factor_confirmed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t02_users');
    }
};
