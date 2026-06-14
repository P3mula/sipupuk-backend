<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'phone',
        'password',
        'role',
        'alamat',
    ];

    // Relasi dengan Toko (untuk penjual)
    public function toko()
    {
        return $this->hasOne(Toko::class);
    }

    // Relasi dengan Pesanan (untuk pembeli)
    public function pesanans()
    {
        return $this->hasMany(Pesanan::class);
    }

    // Scope untuk role
    public function scopeAdmin($query)
    {
        return $query->where('role', 'admin');
    }

    public function scopePenjual($query)
    {
        return $query->where('role', 'penjual');
    }

    public function scopePembeli($query)
    {
        return $query->where('role', 'pembeli');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}