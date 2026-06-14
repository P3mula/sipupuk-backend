<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TokoController;
use App\Http\Controllers\Api\ProdukController;
use App\Http\Controllers\Api\SatuanController;
use App\Http\Controllers\Api\LaporanController;
use App\Http\Controllers\Api\PesananController;
use App\Http\Controllers\Api\KategoriProdukController;

/*
|--------------------------------------------------------------------------
| Public Routes (No Authentication)
|--------------------------------------------------------------------------
*/

// Auth Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/kategori-produk', [KategoriProdukController::class, 'index']);
Route::get('/kategori-produk/{id}', [KategoriProdukController::class, 'show']);
Route::get('/satuan', [SatuanController::class, 'index']);
Route::get('/satuan/{id}', [SatuanController::class, 'show']);
Route::get('/kategori/{kategoriId}/satuan', [SatuanController::class, 'getByKategori']);


// Public Toko Routes
Route::get('/toko', [TokoController::class, 'index']);
Route::get('/toko/{id}', [TokoController::class, 'show']);

/*
|--------------------------------------------------------------------------
| Protected Routes (Need Authentication)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    
    // Auth Routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    /*
    |--------------------------------------------------------------------------
    | ADMIN ROUTES (Web Access)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        
        Route::post('/satuan', [SatuanController::class, 'store']);
        Route::put('/satuan/{id}', [SatuanController::class, 'update']);
        Route::delete('/satuan/{id}', [SatuanController::class, 'destroy']);
        
        // Mapping kategori-satuan
        Route::post('/kategori-satuan/assign', [SatuanController::class, 'assignToKategori']);
        Route::post('/kategori-satuan/remove', [SatuanController::class, 'removeFromKategori']);
        
    });

    /*
    |--------------------------------------------------------------------------
    | PENJUAL ROUTES (Mobile Access)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:penjual')->prefix('penjual')->group(function () {
        
        // Toko Management
        Route::get('/toko', [TokoController::class, 'myToko']);
        Route::post('/toko', [TokoController::class, 'store']);
        Route::put('/toko', [TokoController::class, 'update']);
        
        // Produk Management
        Route::get('/produk', [ProdukController::class, 'myProduk']);
        Route::post('/produk', [ProdukController::class, 'store']);
        Route::put('/produk/{id}', [ProdukController::class, 'update']);
        Route::delete('/produk/{id}', [ProdukController::class, 'destroy']);
        
        // Pesanan Management
        Route::get('/pesanan', [PesananController::class, 'tokoPesanan']);
        Route::get('/pesanan/statistics', [PesananController::class, 'statistics']);
        Route::get('/pesanan/{id}', [PesananController::class, 'show']);
        Route::post('/pesanan/{id}/konfirmasi', [PesananController::class, 'konfirmasi']);
        Route::post('/pesanan/{id}/siap-diambil', [PesananController::class, 'siapDiambil']);
        Route::post('/pesanan/{id}/selesai', [PesananController::class, 'selesai']);
        Route::post('/pesanan/{id}/tolak', [PesananController::class, 'tolak']);

        Route::get('/laporan/bulanan', [LaporanController::class, 'laporanBulanan']);
    });

    /*
    |--------------------------------------------------------------------------
    | PEMBELI ROUTES (Mobile Access)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:pembeli')->prefix('pembeli')->group(function () {
    
        
        // Produk Routes
        Route::get('/produk', [ProdukController::class, 'index']);
        Route::get('/produk/{id}', [ProdukController::class, 'show']);
        
        // Pesanan Routes
        Route::post('/pesanan', [PesananController::class, 'store']);
        Route::get('/pesanan', [PesananController::class, 'myPesanan']);
        Route::get('/pesanan/statistics', [PesananController::class, 'statistics']);
        Route::get('/pesanan/{id}', [PesananController::class, 'show']);
        Route::post('/pesanan/{id}/cancel', [PesananController::class, 'cancel']);
    });

    /*
    |--------------------------------------------------------------------------
    | SHARED ROUTES (All Authenticated Users)
    |--------------------------------------------------------------------------
    */
    
});

/*
|--------------------------------------------------------------------------
| Custom Middleware for Role Checking
|--------------------------------------------------------------------------
*/

// You need to create this middleware
// php artisan make:middleware RoleMiddleware