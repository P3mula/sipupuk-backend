<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SatuanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
     public function run(): void
    {
        $satuans = [
            ['id' => 1, 'nama_satuan' => 'Karung', 'singkatan' => 'krg'],
            ['id' => 2, 'nama_satuan' => 'Kilogram', 'singkatan' => 'kg'],
            ['id' => 3, 'nama_satuan' => 'Liter', 'singkatan' => 'L'],
            ['id' => 4, 'nama_satuan' => 'Botol', 'singkatan' => 'btl'],
            ['id' => 5, 'nama_satuan' => 'Mililiter', 'singkatan' => 'ml'],
            ['id' => 6, 'nama_satuan' => 'Buah', 'singkatan' => 'pcs'],
            ['id' => 7, 'nama_satuan' => 'Unit', 'singkatan' => 'unit'],
            ['id' => 8, 'nama_satuan' => 'Polybag', 'singkatan' => 'plb'],
            ['id' => 9, 'nama_satuan' => 'Pack', 'singkatan' => 'pack'],
            ['id' => 10, 'nama_satuan' => 'Batang', 'singkatan' => 'btg'],
        ];

        DB::table('satuans')->insert($satuans);
    }
}
