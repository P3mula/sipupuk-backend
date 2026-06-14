<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Satuan extends Model
{
    use HasFactory;

    protected $table = 'satuans';

    protected $fillable = [
        'nama_satuan',
        'singkatan',
    ];

    /**
     * Relasi ke produk
     * One satuan -> many produk
     */
    public function produks()
    {
        return $this->hasMany(Produk::class);
    }

    /**
     * Relasi many-to-many ke kategori produk
     */
    public function kategoriProduks()
    {
        return $this->belongsToMany(
            KategoriProduk::class,
            'kategori_satuan',
            'satuan_id',
            'kategori_produk_id'
        )->withTimestamps();
    }
}
