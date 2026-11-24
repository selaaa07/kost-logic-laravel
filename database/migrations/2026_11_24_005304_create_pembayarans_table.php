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
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penghuni_id')->constrained('penghunis')->cascadeOnDelete();
            $table->string('bulan_tagihan', 50);
            $table->decimal('nominal', 12, 2);
            $table->enum('status', ['belum_lunas', 'menunggu', 'lunas'])->default('belum_lunas');
            $table->enum('metode_bayar', ['transfer', 'tunai'])->nullable();
            $table->string('nama_bank', 50)->nullable();
            $table->string('bukti_bayar')->nullable();
            $table->date('tanggal_bayar')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
    }
};
