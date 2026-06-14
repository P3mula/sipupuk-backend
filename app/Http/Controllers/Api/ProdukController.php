<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\Toko;
use App\Models\KategoriProduk;
use App\Models\Satuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ProdukController extends Controller
{
    /**
     * Get all produk by toko penjual yang login
     */
    public function myProduk(Request $request)
    {
        $toko = Toko::where('user_id', $request->user()->id)->first();

        if (!$toko) {
            return response()->json([
                'success' => false,
                'message' => 'Toko belum dibuat'
            ], 404);
        }

        $perPage = $request->input('per_page', 10);
        $kategoriId = $request->input('kategori_id'); // Filter by kategori
        $status = $request->input('status');
        $search = $request->input('search');

        $query = Produk::where('toko_id', $toko->id)
            ->with(['toko', 'kategoriProduk', 'satuan']);

        if ($kategoriId) {
            $query->where('kategori_produk_id', $kategoriId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_produk', 'like', "%{$search}%")
                  ->orWhere('merk', 'like', "%{$search}%");
            });
        }

        $produks = $query->latest()->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Data produk berhasil diambil',
            'data' => $produks
        ], 200);
    }

    /**
     * Create produk
     */
    public function store(Request $request)
    {
        $toko = Toko::where('user_id', $request->user()->id)->first();

        if (!$toko) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum memiliki toko'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'kategori_produk_id' => 'required|exists:kategori_produks,id',
            'satuan_id' => 'required|exists:satuans,id',
            'nama_produk' => 'required|string|max:255',
            'merk' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'berat' => 'nullable|numeric|min:0', // Opsional untuk produk seperti alat
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'foto_produk' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // ✅ Validasi satuan sesuai kategori
        $kategori = KategoriProduk::find($request->kategori_produk_id);
        $satuanValid = $kategori->satuans()->where('satuan_id', $request->satuan_id)->exists();

        if (!$satuanValid) {
            return response()->json([
                'success' => false,
                'message' => 'Satuan tidak sesuai dengan kategori produk'
            ], 422);
        }

        try {
            $data = $request->only([
                'kategori_produk_id',
                'satuan_id',
                'nama_produk',
                'merk',
                'deskripsi',
                'berat',
                'harga',
                'stok'
            ]);
            
            $data['toko_id'] = $toko->id;

            // Handle foto upload
            if ($request->hasFile('foto_produk')) {
                $file = $request->file('foto_produk');
                
                if (!$file->isValid()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'File upload gagal'
                    ], 400);
                }

                $filename = time() . '_' . $file->getClientOriginalName();
                
                // ✅ Gunakan absolute path
                $destinationPath = storage_path('app/public/produk');
                
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
                
                $file->move($destinationPath, $filename);
                $data['foto_produk'] = 'produk/' . $filename;
                
                // Verifikasi file tersimpan
                if (!file_exists($destinationPath . '/' . $filename)) {
                    throw new \Exception('File berhasil diupload tapi tidak ditemukan di storage');
                }
            }

            // Set status based on stok
            $data['status'] = $data['stok'] > 0 ? 'tersedia' : 'habis';

            $produk = Produk::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil ditambahkan',
                'data' => $produk->load(['toko', 'kategoriProduk', 'satuan'])
            ], 201);

        } catch (\Exception $e) {
            // Hapus file jika ada error saat save database
            if (isset($data['foto_produk']) && file_exists(storage_path('app/public/' . $data['foto_produk']))) {
                Storage::disk('public')->delete($data['foto_produk']);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan produk: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update produk
     */
    public function update(Request $request, $id)
    {
        $toko = Toko::where('user_id', $request->user()->id)->first();

        if (!$toko) {
            return response()->json([
                'success' => false,
                'message' => 'Toko tidak ditemukan'
            ], 404);
        }

        $produk = Produk::where('toko_id', $toko->id)->find($id);

        if (!$produk) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'kategori_produk_id' => 'required|exists:kategori_produks,id',
            'satuan_id' => 'required|exists:satuans,id',
            'nama_produk' => 'required|string|max:255',
            'merk' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'berat' => 'nullable|numeric|min:0',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'foto_produk' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'nullable|in:tersedia,habis,nonaktif'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // ✅ Validasi satuan sesuai kategori
        $kategori = KategoriProduk::find($request->kategori_produk_id);
        $satuanValid = $kategori->satuans()->where('satuan_id', $request->satuan_id)->exists();

        if (!$satuanValid) {
            return response()->json([
                'success' => false,
                'message' => 'Satuan tidak sesuai dengan kategori produk'
            ], 422);
        }

        try {
            $data = $request->only([
                'kategori_produk_id',
                'satuan_id',
                'nama_produk',
                'merk',
                'deskripsi',
                'berat',
                'harga',
                'stok'
            ]);

            // Handle foto upload
            if ($request->hasFile('foto_produk')) {
                $file = $request->file('foto_produk');
                
                if (!$file->isValid()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'File upload gagal'
                    ], 400);
                }

                // Delete old photo
                if ($produk->foto_produk) {
                    Storage::disk('public')->delete($produk->foto_produk);
                }

                $filename = time() . '_' . $file->getClientOriginalName();
                $destinationPath = storage_path('app/public/produk');
                
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
                
                $file->move($destinationPath, $filename);
                $data['foto_produk'] = 'produk/' . $filename;
            }

            // Auto set status based on stok if not manually set
            if (!$request->has('status')) {
                $data['status'] = $data['stok'] > 0 ? 'tersedia' : 'habis';
            } else {
                $data['status'] = $request->status;
            }

            $produk->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil diupdate',
                'data' => $produk->load(['toko', 'kategoriProduk', 'satuan'])
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate produk: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete produk
     */
    public function destroy(Request $request, $id)
    {
        $toko = Toko::where('user_id', $request->user()->id)->first();

        if (!$toko) {
            return response()->json([
                'success' => false,
                'message' => 'Toko tidak ditemukan'
            ], 404);
        }

        $produk = Produk::where('toko_id', $toko->id)->find($id);

        if (!$produk) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }

        // Check if produk has orders
        if ($produk->detailPesanans()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak dapat dihapus karena sudah ada pesanan'
            ], 400);
        }

        // Delete photo if exists
        if ($produk->foto_produk) {
            Storage::disk('public')->delete($produk->foto_produk);
        }

        $produk->delete();

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil dihapus'
        ], 200);
    }

    /**
     * Get all produk untuk pembeli - MOBILE
     * Filter by kategori (Pupuk, Bibit, Pakan, Alat, Pestisida)
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
        $kategoriId = $request->input('kategori_id'); // Filter by kategori
        $tokoId = $request->input('toko_id');

        $query = Produk::where('status', 'tersedia')
            ->with(['toko', 'toko.user', 'kategoriProduk', 'satuan']);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_produk', 'like', "%{$search}%")
                  ->orWhere('merk', 'like', "%{$search}%");
            });
        }

        if ($kategoriId) {
            $query->where('kategori_produk_id', $kategoriId);
        }

        if ($tokoId) {
            $query->where('toko_id', $tokoId);
        }

        $produks = $query->latest()->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Data produk berhasil diambil',
            'data' => $produks
        ], 200);
    }

    /**
     * Get single produk detail
     */
    public function show($id)
    {
        $produk = Produk::with(['toko', 'toko.user', 'kategoriProduk', 'satuan'])->find($id);

        if (!$produk) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data produk berhasil diambil',
            'data' => $produk
        ], 200);
    }

    /**
     * Get list kategori produk (untuk dropdown di mobile)
     */
    public function getKategori()
    {
        $kategoris = KategoriProduk::where('is_active', true)
            ->orderBy('nama_kategori')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Data kategori berhasil diambil',
            'data' => $kategoris
        ], 200);
    }

    /**
     * Get satuan by kategori (untuk dropdown satuan dinamis)
     */
    public function getSatuanByKategori($kategoriId)
    {
        $kategori = KategoriProduk::with('satuans')->find($kategoriId);

        if (!$kategori) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data satuan berhasil diambil',
            'data' => $kategori->satuans
        ], 200);
    }
}