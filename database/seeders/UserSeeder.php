<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        User::create([
            'name' => 'Admin Pupuk',
            'phone' => '081234567890',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        // Penjual Users
        User::create([
            'name' => 'Toko Tani Jaya',
            'phone' => '081234567891',
            'password' => Hash::make('penjual123'),
            'role' => 'penjual',
        ]);

        User::create([
            'name' => 'Toko Subur Makmur',
            'phone' => '081234567892',
            'password' => Hash::make('penjual123'),
            'role' => 'penjual',
        ]);

        // Pembeli Users
        User::create([
            'name' => 'Budi Santoso',
            'phone' => '081234567893',
            'password' => Hash::make('pembeli123'),
            'role' => 'pembeli',
        ]);

        User::create([
            'name' => 'Siti Aminah',
            'phone' => '081234567894',
            'password' => Hash::make('pembeli123'),
            'role' => 'pembeli',
        ]);

        User::create([
            'name' => 'Ahmad Hidayat',
            'phone' => '081234567895',
            'password' => Hash::make('pembeli123'),
            'role' => 'pembeli',
        ]);
    }
}