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
        Schema::create('satuans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_satuan'); // karung, kg, liter, buah, dll
            $table->string('singkatan')->nullable(); // kg, L, pcs, dll
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('satuans');
    }
};
