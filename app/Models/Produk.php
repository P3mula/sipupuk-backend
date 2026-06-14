<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    protected $fillable = [
        'toko_id',
        'kategori_produk_id',
        'satuan_id',
        'nama_produk',
        'merk',
        'deskripsi',
        'berat',
        'harga',
        'stok',
        'foto_produk',
        'status'
    ];

    protected $casts = [
        'berat' => 'decimal:2',
        'harga' => 'decimal:2'
    ];

    protected $appends = ['foto_produk_url'];

    // ✅ Relasi dengan Kategori Produk
    public function kategoriProduk()
    {
        return $this->belongsTo(KategoriProduk::class);
    }

    // ✅ Relasi dengan Satuan
    public function satuan()
    {
        return $this->belongsTo(Satuan::class);
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

    // Scope untuk produk tersedia
    public function scopeTersedia($query)
    {
        return $query->where('status', 'tersedia')->where('stok', '>', 0);
    }

    // ✅ Accessor untuk URL foto
    public function getFotoProdukUrlAttribute()
    {
        if (!$this->foto_produk) {
            return null;
        }
        
        return url('storage/' . $this->foto_produk);
    }
}