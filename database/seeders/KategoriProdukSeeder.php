<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class KategoriProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategoris = [
            ['id' => 1, 'nama_kategori' => 'Pupuk', 'icon' => '🌾', 'is_active' => true],
            ['id' => 2, 'nama_kategori' => 'Bibit', 'icon' => '🌱', 'is_active' => true],
            ['id' => 3, 'nama_kategori' => 'Pakan Ternak', 'icon' => '🐔', 'is_active' => true],
            ['id' => 4, 'nama_kategori' => 'Alat Pertanian', 'icon' => '🔧', 'is_active' => true],
            ['id' => 5, 'nama_kategori' => 'Pestisida', 'icon' => '🧪', 'is_active' => true],
        ];

        DB::table('kategori_produks')->insert($kategoris);
    }
}
