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
        Schema::create('penghuni_rumah', function (Blueprint $table) {
            $table->id('id_penghuni_rumah');
            $table->unsignedBigInteger('id_penghuni');
            $table->foreign('id_penghuni')->references('id_penghuni')->on('penghuni');
            $table->unsignedBigInteger('id_rumah');
            $table->foreign('id_rumah')->references('id_rumah')->on('rumah');
            $table->date('tanggal_masuk');
            $table->date('tanggal_keluar');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penghuni_rumah');
    }
};
