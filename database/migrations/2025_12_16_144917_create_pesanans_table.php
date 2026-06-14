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
        Schema::create('pesanans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pesanan')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Pembeli
            $table->foreignId('toko_id')->constrained()->onDelete('cascade');
            $table->decimal('total_harga', 12, 2);
            $table->integer('total_item');
            $table->enum('status', [
                'pending',          // Menunggu konfirmasi toko
                'dikonfirmasi',     // Toko konfirmasi, pesanan diproses
                'siap_diambil',     // Pesanan siap diambil
                'selesai',          // Pesanan sudah diambil & dibayar
                'dibatalkan'        // Dibatalkan oleh pembeli atau toko
            ])->default('pending');
            $table->text('catatan_pembeli')->nullable();
            $table->text('catatan_toko')->nullable();
            $table->timestamp('tanggal_konfirmasi')->nullable();
            $table->timestamp('tanggal_siap')->nullable();
            $table->timestamp('tanggal_selesai')->nullable();
            $table->timestamps();
            
            // Index untuk optimasi
            $table->index('kode_pesanan');
            $table->index('status');
            $table->index(['user_id', 'status']);
            $table->index(['toko_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesanans');
    }
};