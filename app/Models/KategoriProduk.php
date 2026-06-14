<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriProduk extends Model
{
    use HasFactory;

    protected $table = 'kategori_produks';

    protected $fillable = [
        'nama_kategori',
        'icon',
        'deskripsi',
        'is_active',
    ];

    /**
     * Relasi ke produk
     * One kategori -> many produk
     */
    public function produks()
    {
        return $this->hasMany(Produk::class);
    }

    /**
     * Relasi many-to-many ke satuan
     */
    public function satuans()
    {
        return $this->belongsToMany(
            Satuan::class,
            'kategori_satuan',
            'kategori_produk_id',
            'satuan_id'
        )->withTimestamps();
    }
}
