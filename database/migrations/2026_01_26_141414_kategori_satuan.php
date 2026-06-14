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
        Schema::create('kategori_satuan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_produk_id')->constrained('kategori_produks')->onDelete('cascade');
            $table->foreignId('satuan_id')->constrained('satuans')->onDelete('cascade');
            $table->timestamps();
            
            // Prevent duplicate mapping
            $table->unique(['kategori_produk_id', 'satuan_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kategori_satuan');
    }
};
