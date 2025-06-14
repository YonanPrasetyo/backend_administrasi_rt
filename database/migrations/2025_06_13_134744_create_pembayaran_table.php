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
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id('id_pembayaran');
            $table->unsignedBigInteger('id_penghuni');
            $table->foreign('id_penghuni')->references('id_penghuni')->on('penghuni');
            $table->unsignedBigInteger('id_rumah');
            $table->foreign('id_rumah')->references('id_rumah')->on('rumah');
            $table->year('tahun');
            $table->enum('bulan', [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]);
            $table->enum('jenis', ['iuran satpam', 'iuran kebersihan']);
            $table->integer('total');
            $table->date('tanggal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
