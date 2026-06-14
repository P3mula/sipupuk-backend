<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPesanan extends Model
{
    use HasFactory;

    protected $fillable = [
        'pesanan_id',
        'produk_id',
        'nama_produk',
        'merk',
        'kategori_produk',
        'harga',
        'jumlah',
        'satuan',
        'subtotal',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'subtotal' => 'decimal:2'
    ];

    // Relasi dengan Pesanan
    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class);
    }

    // Relasi dengan Produk
    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}