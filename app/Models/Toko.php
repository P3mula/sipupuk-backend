<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Toko extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama_toko',
        'alamat',
        'desa',
        'no_telepon',
        'deskripsi',
        // 'foto_toko',
        'status',
        'jam_buka',
        'jam_tutup'
    ];

    // Relasi dengan User (Penjual)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi dengan Produk
    public function produks()
    {
        return $this->hasMany(Produk::class);
    }

    // Relasi dengan Pesanan
    public function pesanans()
    {
        return $this->hasMany(Pesanan::class);
    }

    // Scope untuk toko aktif
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }
}