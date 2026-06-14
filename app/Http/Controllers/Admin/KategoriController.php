<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KategoriProduk;

class KategoriController extends Controller
{
    /**
     * Display listing
     */
    public function index()
    {
        $kategoris = KategoriProduk::withCount('produks')
            ->orderBy('nama_kategori', 'asc')
            ->paginate(20);

        return view('admin.kategori.index', compact('kategoris'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('admin.kategori.create');
    }

    /**
     * Store new kategori
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:100|unique:kategori_produks,nama_kategori',
            'icon' => 'nullable|string|max:10',
            'deskripsi' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        KategoriProduk::create($validated);

        return redirect()->route('admin.kategori.index')
            ->with('success', 'Kategori berhasil ditambahkan!');
    }

    /**
     * Show edit form
     */
    public function edit(KategoriProduk $kategori)
    {
        return view('admin.kategori.create', compact('kategori'));
    }

    /**
     * Update kategori
     */
    public function update(Request $request, KategoriProduk $kategori)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:100|unique:kategori_produks,nama_kategori,' . $kategori->id,
            'icon' => 'nullable|string|max:10',
            'deskripsi' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        $kategori->update($validated);

        return redirect()->route('admin.kategori.index')
            ->with('success', 'Kategori berhasil diupdate!');
    }

    /**
     * Delete kategori
     */
    public function destroy(KategoriProduk $kategori)
    {
        // Check if kategori has products
        if ($kategori->produks()->count() > 0) {
            return back()->with('error', 'Kategori tidak dapat dihapus karena masih memiliki produk!');
        }

        $kategori->delete();

        return redirect()->route('admin.kategori.index')
            ->with('success', 'Kategori berhasil dihapus!');
    }

    /**
     * Toggle active status
     */
    public function toggleActive(KategoriProduk $kategori)
    {
        $kategori->update([
            'is_active' => !$kategori->is_active
        ]);

        return back()->with('success', 'Status kategori berhasil diubah!');
    }
}