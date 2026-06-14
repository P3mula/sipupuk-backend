<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\Admin\ProdukAdminController;
use App\Http\Controllers\Admin\SatuanAdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


// Admin Login Routes
Route::get('/admin/login', [AdminController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login.submit');

// Admin Protected Routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    
    // Logout
    Route::post('/logout', [AdminController::class, 'logout'])->name('logout');
    
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // // Kategori CRUD
    Route::resource('kategori', KategoriController::class);
    Route::post('kategori/{kategori}/toggle', [KategoriController::class, 'toggleActive'])->name('kategori.toggle');
    
    // // Satuan CRUD
    Route::resource('satuan', SatuanAdminController::class);
    Route::get('satuan-mapping', [SatuanAdminController::class, 'mapping'])->name('satuan.mapping');
    Route::post('satuan-mapping/{kategori}', [SatuanAdminController::class, 'updateMapping'])->name('satuan.mapping.update');
    
    // Produk Management
    Route::get('produk', [ProdukAdminController::class, 'index'])->name('produk.index');
    Route::get('produk/{produk}', [ProdukAdminController::class, 'show'])->name('produk.show');
    Route::delete('produk/{produk}', [ProdukAdminController::class, 'destroy'])->name('produk.destroy');
    Route::post('produk/{produk}/toggle-status', [ProdukAdminController::class, 'toggleStatus'])->name('produk.toggle-status');

});