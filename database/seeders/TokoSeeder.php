<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Toko;
use App\Models\User;

class TokoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil user penjual
        $penjual1 = User::where('phone', '081234567891')->first();
        $penjual2 = User::where('phone', '081234567892')->first();

        $tokos = [
            [
                'user_id' => $penjual1->id,
                'nama_toko' => 'Toko Tani Jaya',
                'alamat' => 'Jl. Pasar Lama No. 15',
                'desa' => 'Lumban Julu',
                'no_telepon' => '081234567891',
                'deskripsi' => 'Toko pupuk terlengkap dan terpercaya di Balige. Melayani pembelian subsidi dan non-subsidi.',
                'status' => 'aktif',
                'jam_buka' => '08:00',
                'jam_tutup' => '17:00'
            ],
            [
                'user_id' => $penjual2->id,
                'nama_toko' => 'Toko Subur Makmur',
                'alamat' => 'Jl. Sisingamangaraja No. 88',
                'desa' => 'Sigaol',
                'no_telepon' => '081234567892',
                'deskripsi' => 'Pusat pupuk berkualitas dengan harga bersaing. Tersedia berbagai jenis pupuk untuk pertanian Anda.',
                'status' => 'aktif',
                'jam_buka' => '07:30',
                'jam_tutup' => '18:00'
            ]
        ];

        foreach ($tokos as $toko) {
            Toko::firstOrCreate($toko);
        }
    }
}