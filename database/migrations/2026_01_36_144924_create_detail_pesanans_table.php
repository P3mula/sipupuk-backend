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
        Schema::create('detail_pesanans', function (Blueprint $table) {
            $table->id();

            // Relasi
            $table->foreignId('pesanan_id')
                ->constrained()
                ->onDelete('cascade');

            $table->foreignId('produk_id')
                ->constrained()
                ->onDelete('cascade');

            // Snapshot data produk (history)
            $table->string('nama_produk');
            $table->string('merk');
            $table->string('kategori_produk')->nullable(); // simpan nama kategori saat transaksi
            $table->string('satuan'); // karung, kg, dll

            // Transaksi
            $table->decimal('harga', 10, 2);
            $table->integer('jumlah');
            $table->decimal('subtotal', 12, 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_pesanans');
    }
};