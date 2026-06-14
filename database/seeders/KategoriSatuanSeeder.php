<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class KategoriSatuanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mappings = [
            // Pupuk (id: 1) → Karung, Kilogram
            ['kategori_produk_id' => 1, 'satuan_id' => 1], // Karung
            ['kategori_produk_id' => 1, 'satuan_id' => 2], // Kilogram
            
            // Bibit (id: 2) → Polybag, Pack, Buah, Batang
            ['kategori_produk_id' => 2, 'satuan_id' => 8], // Polybag
            ['kategori_produk_id' => 2, 'satuan_id' => 9], // Pack
            ['kategori_produk_id' => 2, 'satuan_id' => 6], // Buah
            ['kategori_produk_id' => 2, 'satuan_id' => 10], // Batang
            
            // Pakan Ternak (id: 3) → Karung, Kilogram, Pack
            ['kategori_produk_id' => 3, 'satuan_id' => 1], // Karung
            ['kategori_produk_id' => 3, 'satuan_id' => 2], // Kilogram
            ['kategori_produk_id' => 3, 'satuan_id' => 9], // Pack
            
            // Alat Pertanian (id: 4) → Buah, Unit
            ['kategori_produk_id' => 4, 'satuan_id' => 6], // Buah
            ['kategori_produk_id' => 4, 'satuan_id' => 7], // Unit
            
            // Pestisida (id: 5) → Liter, Botol, Mililiter
            ['kategori_produk_id' => 5, 'satuan_id' => 3], // Liter
            ['kategori_produk_id' => 5, 'satuan_id' => 4], // Botol
            ['kategori_produk_id' => 5, 'satuan_id' => 5], // Mililiter
        ];

        DB::table('kategori_satuan')->insert($mappings);
    }
}
