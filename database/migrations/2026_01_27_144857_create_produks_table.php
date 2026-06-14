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
        Schema::create('produks', function (Blueprint $table) {
            $table->id();

            // Relasi utama
            $table->foreignId('toko_id')
                ->constrained()
                ->onDelete('cascade');

            $table->foreignId('kategori_produk_id')
                ->nullable()
                ->constrained('kategori_produks')
                ->onDelete('restrict');

            $table->foreignId('satuan_id')
                ->nullable()
                ->constrained('satuans')
                ->onDelete('restrict');

            // Informasi produk
            $table->string('nama_produk');
            $table->string('merk'); 
            $table->text('deskripsi')->nullable();

            // Spesifikasi
            $table->decimal('berat', 8, 2)->nullable(); // contoh: 50.00
            $table->decimal('harga', 10, 2);
            $table->integer('stok');

            // Media & status
            $table->string('foto_produk')->nullable();
            $table->enum('status', ['tersedia', 'habis', 'nonaktif'])
                ->default('tersedia');

            $table->timestamps();

            // Index
            $table->index('kategori_produk_id');
            $table->index('toko_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produks');
    }
};