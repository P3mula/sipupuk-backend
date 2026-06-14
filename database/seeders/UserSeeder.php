<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin User
        User::firstOrCreate([
            'name' => 'Admin Pupuk',
            'phone' => '081234567890',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        // Penjual Users
        User::firstOrCreate([
            'name' => 'Toko Tani Jaya',
            'phone' => '081234567891',
            'password' => Hash::make('penjual123'),
            'role' => 'penjual',
        ]);

        User::firstOrCreate([
            'name' => 'Toko Subur Makmur',
            'phone' => '081234567892',
            'password' => Hash::make('penjual123'),
            'role' => 'penjual',
        ]);

        // Pembeli Users
        User::firstOrCreate([
            'name' => 'Budi Santoso',
            'phone' => '081234567893',
            'password' => Hash::make('pembeli123'),
            'role' => 'pembeli',
        ]);

        User::firstOrCreate([
            'name' => 'Siti Aminah',
            'phone' => '081234567894',
            'password' => Hash::make('pembeli123'),
            'role' => 'pembeli',
        ]);

        User::firstOrCreate([
            'name' => 'Ahmad Hidayat',
            'phone' => '081234567895',
            'password' => Hash::make('pembeli123'),
            'role' => 'pembeli',
        ]);
    }
}