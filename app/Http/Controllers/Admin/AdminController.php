<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Toko;
use App\Models\Produk;
use App\Models\KategoriProduk;
use App\Models\Satuan;

class AdminController extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm()
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        
        return view('admin.login');
    }

    /**
     * Handle login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            if ($user->role !== 'admin') {
                Auth::logout();
                return back()->withErrors([
                    'phone' => 'Anda tidak memiliki akses admin.',
                ])->withInput();
            }

            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'phone' => 'Nomor telepon atau password salah.',
        ])->withInput();
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('admin.login');
    }

    /**
     * Show dashboard
     */
    public function dashboard()
    {
        $stats = [
            'total_users' => User::where('role', '!=', 'admin')->count(),
            'total_pembeli' => User::where('role', 'pembeli')->count(),
            'total_penjual' => User::where('role', 'penjual')->count(),
            'total_toko' => Toko::count(),
            'total_produk' => Produk::count(),
            'total_kategori' => KategoriProduk::count(),
            'total_satuan' => Satuan::count(),
            'produk_tersedia' => Produk::where('status', 'tersedia')->count(),
            'produk_habis' => Produk::where('status', 'habis')->count(),
        ];

        // Latest products
        $latest_products = Produk::with(['toko', 'kategoriProduk', 'satuan'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Latest users
        $latest_users = User::where('role', '!=', 'admin')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'latest_products', 'latest_users'));
    }
}