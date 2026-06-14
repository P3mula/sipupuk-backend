<?php

namespace Database\Seeders;

use App\Models\KategoriProduk;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Produk;
use App\Models\Toko;
use App\Models\Satuan;

class ProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil toko
        $toko1 = Toko::where('nama_toko', 'Toko Tani Jaya')->first();
        $toko2 = Toko::where('nama_toko', 'Toko Subur Makmur')->first();

        $kp1 = KategoriProduk::where('id', '1')->first();
        $kp2 = KategoriProduk::where('id', '2')->first();

        $sa1 = Satuan::where('id', '1')->first();
        $sa2 = Satuan::where('id', '2')->first();
        
        $produks = [
            [
                'toko_id' => $toko1->id,
                'kategori_produk_id' => $kp1->id,
                'satuan_id' => $sa1->id,
                'nama_produk' => 'Pupuk Urea Pril',
                'merk' => 'Pupuk Urea Pril',
                'deskripsi' => 'Pupuk Urea berkualitas tinggi untuk meningkatkan pertumbuhan tanaman. Cocok untuk semua jenis tanaman.',
                'berat' => 50.00,
                'harga' => 250000.00,
                'stok' => 100,
                'foto_produk' => null,
                'status' => 'tersedia'
            ],            

            [
                'toko_id' => $toko2->id,
                'kategori_produk_id' => $kp2->id,
                'satuan_id' => $sa2->id,
                'nama_produk' => 'Pupuk Urea Kualitas Premium',
                'merk' => 'Pupuk Urea Premium',
                'deskripsi' => 'Pupuk Urea kualitas premium dengan kandungan nitrogen tinggi.',
                'berat' => 50.00,
                'harga' => 270000.00,
                'stok' => 90,
                'foto_produk' => null,
                'status' => 'tersedia'
            ],

            
        ];

        foreach ($produks as $produk) {
            Produk::firstOrCreate($produk);
        }
    }
}