<?php

namespace App\Http\Controllers\Admin;

use Storage;
use App\Models\Produk;
use Illuminate\Http\Request;
use App\Models\KategoriProduk;
use App\Http\Controllers\Controller;

class ProdukAdminController extends Controller
{
    /**
     * Display listing
     */
    public function index(Request $request)
    {
        $query = Produk::with(['toko.user', 'kategoriProduk', 'satuan']);

        // Filter by kategori
        if ($request->filled('kategori_id')) {
            $query->where('kategori_produk_id', $request->kategori_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_produk', 'like', "%{$search}%")
                  ->orWhere('merk', 'like', "%{$search}%");
            });
        }

        $produks = $query->orderBy('created_at', 'desc')->paginate(20);
        $kategoris = KategoriProduk::orderBy('nama_kategori', 'asc')->get();

        return view('admin.produk.index', compact('produks', 'kategoris'));
    }

    /**
     * Show detail produk
     */
    public function show(Produk $produk)
    {
        $produk->load(['toko.user', 'kategoriProduk', 'satuan']);
        return view('admin.produk.show', compact('produk'));
    }

    /**
     * Delete produk
     */
    // public function destroy(Produk $produk)
    // {
    //     // Delete foto if exists
    //     if ($produk->foto_produk) {
    //         Storage::disk('public')->delete($produk->foto_produk);
    //     }

    //     $produk->delete();

    //     return redirect()->route('admin.produk.index')
    //         ->with('success', 'Produk berhasil dihapus!');
    // }

    /**
     * Toggle status produk
     */
    public function toggleStatus(Produk $produk)
    {
        $newStatus = $produk->status === 'tersedia' ? 'habis' : 'tersedia';
        
        $produk->update([
            'status' => $newStatus
        ]);

        return back()->with('success', 'Status produk berhasil diubah!');
    }
}