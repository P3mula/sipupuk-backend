<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_pesanan',
        'user_id',
        'toko_id',
        'total_harga',
        'total_item',
        'status',
        'catatan_pembeli',
        'catatan_toko',
        'tanggal_konfirmasi',
        'tanggal_siap',
        'tanggal_selesai'
    ];

    protected $casts = [
        'total_harga' => 'decimal:2',
        'tanggal_konfirmasi' => 'datetime',
        'tanggal_siap' => 'datetime',
        'tanggal_selesai' => 'datetime'
    ];

    // Relasi dengan User (Pembeli)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi dengan Toko
    public function toko()
    {
        return $this->belongsTo(Toko::class);
    }

    // Relasi dengan Detail Pesanan
    public function detailPesanans()
    {
        return $this->hasMany(DetailPesanan::class);
    }

    // Scope untuk filter status
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeDikonfirmasi($query)
    {
        return $query->where('status', 'dikonfirmasi');
    }

    public function scopeSiapDiambil($query)
    {
        return $query->where('status', 'siap_diambil');
    }

    public function scopeSelesai($query)
    {
        return $query->where('status', 'selesai');
    }

    public function scopeDibatalkan($query)
    {
        return $query->where('status', 'dibatalkan');
    }

    // Generate Kode Pesanan
    public static function generateKodePesanan()
    {
        $prefix = 'PSN';
        $date = date('Ymd');
        $random = strtoupper(substr(md5(microtime()), 0, 4));
        return $prefix . $date . $random;
    }
}