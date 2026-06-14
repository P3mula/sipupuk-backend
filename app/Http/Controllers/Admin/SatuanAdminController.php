<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Satuan;
use App\Models\KategoriProduk;

class SatuanAdminController extends Controller
{
    /**
     * Display listing
     */
    public function index()
    {
        $satuans = Satuan::withCount('produks')
            ->orderBy('nama_satuan', 'asc')
            ->paginate(20);

        return view('admin.satuan.index', compact('satuans'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $kategoris = KategoriProduk::where('is_active', true)
            ->orderBy('nama_kategori', 'asc')
            ->get();

        return view('admin.satuan.create', compact('kategoris'));
    }

    /**
     * Store new satuan
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_satuan' => 'required|string|max:50|unique:satuans,nama_satuan',
            'singkatan' => 'nullable|string|max:10',
            'kategori_ids' => 'nullable|array',
            'kategori_ids.*' => 'exists:kategori_produks,id',
        ]);

        $satuan = Satuan::create([
            'nama_satuan' => $validated['nama_satuan'],
            'singkatan' => $validated['singkatan'],
        ]);

        // ✅ Attach to categories if selected
        if (!empty($validated['kategori_ids'])) {
            $satuan->kategoriProduks()->attach($validated['kategori_ids']);
        }

        return redirect()->route('admin.satuan.index')
            ->with('success', 'Satuan berhasil ditambahkan!');
    }

    /**
     * Show edit form
     */
    public function edit(Satuan $satuan)
    {
        // ✅ Load relasi kategoriProduks dengan eager loading
        $satuan->load('kategoriProduks');
        
        $kategoris = KategoriProduk::where('is_active', true)
            ->orderBy('nama_kategori', 'asc')
            ->get();

        // ✅ Gunakan kategoriProduks (sesuai nama relasi di model)
        $selectedKategoris = $satuan->kategoriProduks?->pluck('id')->toArray() ?? [];

        return view('admin.satuan.create', compact('satuan', 'kategoris', 'selectedKategoris'));
    }

    /**
     * Update satuan
     */
    public function update(Request $request, Satuan $satuan)
    {
        $validated = $request->validate([
            'nama_satuan' => 'required|string|max:50|unique:satuans,nama_satuan,' . $satuan->id,
            'singkatan' => 'nullable|string|max:10',
            'kategori_ids' => 'nullable|array',
            'kategori_ids.*' => 'exists:kategori_produks,id',
        ]);

        $satuan->update([
            'nama_satuan' => $validated['nama_satuan'],
            'singkatan' => $validated['singkatan'],
        ]);

        // ✅ Sync categories
        if (isset($validated['kategori_ids'])) {
            $satuan->kategoriProduks()->sync($validated['kategori_ids']);
        } else {
            $satuan->kategoriProduks()->detach();
        }

        return redirect()->route('admin.satuan.index')
            ->with('success', 'Satuan berhasil diupdate!');
    }

    /**
     * Delete satuan
     */
    public function destroy(Satuan $satuan)
    {
        // Check if satuan has products
        if ($satuan->produks()->count() > 0) {
            return back()->with('error', 'Satuan tidak dapat dihapus karena masih digunakan oleh produk!');
        }

        $satuan->delete();

        return redirect()->route('admin.satuan.index')
            ->with('success', 'Satuan berhasil dihapus!');
    }

    /**
     * Show mapping page
     */
    public function mapping()
    {
        $kategoris = KategoriProduk::with('satuans')
            ->where('is_active', true)
            ->orderBy('nama_kategori', 'asc')
            ->get();

        $allSatuans = Satuan::orderBy('nama_satuan', 'asc')->get();

        return view('admin.satuan.mapping', compact('kategoris', 'allSatuans'));
    }

    /**
     * Update mapping
     */
    public function updateMapping(Request $request, KategoriProduk $kategori)
    {
        $validated = $request->validate([
            'satuan_ids' => 'nullable|array',
            'satuan_ids.*' => 'exists:satuans,id',
        ]);

        if (isset($validated['satuan_ids'])) {
            $kategori->satuans()->sync($validated['satuan_ids']);
        } else {
            $kategori->satuans()->detach();
        }

        return back()->with('success', 'Mapping satuan berhasil diupdate!');
    }
}