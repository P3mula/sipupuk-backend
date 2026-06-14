<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Satuan;
use App\Models\KategoriProduk;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SatuanController extends Controller
{
    /**
     * Mendapatkan semua satuan
     * 
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $satuanList = Satuan::orderBy('nama_satuan', 'asc')
                ->get()
                ->map(function ($satuan) {
                    return [
                        'id' => $satuan->id,
                        'namaSatuan' => $satuan->nama_satuan,
                        'singkatan' => $satuan->singkatan,
                        'createdAt' => $satuan->created_at?->toIso8601String(),
                        'updatedAt' => $satuan->updated_at?->toIso8601String(),
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'Satuan berhasil diambil',
                'data' => $satuanList,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil satuan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mendapatkan satuan berdasarkan kategori produk
     * 
     * @param int $kategoriId
     * @return JsonResponse
     */
    public function getByKategori(int $kategoriId): JsonResponse
    {
        try {
            $kategori = KategoriProduk::findOrFail($kategoriId);
            
            $satuanList = $kategori->satuans()
                ->orderBy('nama_satuan', 'asc')
                ->get()
                ->map(function ($satuan) {
                    return [
                        'id' => $satuan->id,
                        'namaSatuan' => $satuan->nama_satuan,
                        'singkatan' => $satuan->singkatan,
                        'createdAt' => $satuan->created_at?->toIso8601String(),
                        'updatedAt' => $satuan->updated_at?->toIso8601String(),
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'Satuan untuk kategori berhasil diambil',
                'data' => $satuanList,
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan',
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil satuan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mendapatkan detail satuan berdasarkan ID
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $satuan = Satuan::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Detail satuan berhasil diambil',
                'data' => [
                    'id' => $satuan->id,
                    'namaSatuan' => $satuan->nama_satuan,
                    'singkatan' => $satuan->singkatan,
                    'jumlahKategori' => $satuan->kategoris()->count(),
                    'createdAt' => $satuan->created_at?->toIso8601String(),
                    'updatedAt' => $satuan->updated_at?->toIso8601String(),
                ],
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Satuan tidak ditemukan',
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail satuan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Membuat satuan baru (untuk admin)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'nama_satuan' => 'required|string|max:50|unique:satuans,nama_satuan',
                'singkatan' => 'nullable|string|max:10',
            ]);

            $satuan = Satuan::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Satuan berhasil dibuat',
                'data' => [
                    'id' => $satuan->id,
                    'namaSatuan' => $satuan->nama_satuan,
                    'singkatan' => $satuan->singkatan,
                ],
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat satuan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update satuan (untuk admin)
     * 
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $satuan = Satuan::findOrFail($id);

            $validated = $request->validate([
                'nama_satuan' => 'sometimes|string|max:50|unique:satuans,nama_satuan,' . $id,
                'singkatan' => 'nullable|string|max:10',
            ]);

            $satuan->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Satuan berhasil diupdate',
                'data' => [
                    'id' => $satuan->id,
                    'namaSatuan' => $satuan->nama_satuan,
                    'singkatan' => $satuan->singkatan,
                ],
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Satuan tidak ditemukan',
            ], 404);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate satuan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Hapus satuan (untuk admin)
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $satuan = Satuan::findOrFail($id);

            // Cek apakah satuan masih digunakan oleh produk
            $produkCount = $satuan->produks()->count();
            if ($produkCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Satuan tidak dapat dihapus karena masih digunakan oleh produk',
                    'jumlahProduk' => $produkCount,
                ], 409);
            }

            $satuan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Satuan berhasil dihapus',
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Satuan tidak ditemukan',
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus satuan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Assign satuan ke kategori (untuk admin)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function assignToKategori(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'kategori_produk_id' => 'required|exists:kategori_produks,id',
                'satuan_id' => 'required|exists:satuans,id',
            ]);

            $kategori = KategoriProduk::findOrFail($validated['kategori_produk_id']);
            
            // Attach satuan ke kategori (akan skip jika sudah ada karena unique constraint)
            if (!$kategori->satuans()->where('satuan_id', $validated['satuan_id'])->exists()) {
                $kategori->satuans()->attach($validated['satuan_id']);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Satuan berhasil ditambahkan ke kategori',
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Satuan sudah terdaftar di kategori ini',
                ], 409);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan satuan ke kategori',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove satuan dari kategori (untuk admin)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function removeFromKategori(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'kategori_produk_id' => 'required|exists:kategori_produks,id',
                'satuan_id' => 'required|exists:satuans,id',
            ]);

            $kategori = KategoriProduk::findOrFail($validated['kategori_produk_id']);
            $kategori->satuans()->detach($validated['satuan_id']);

            return response()->json([
                'success' => true,
                'message' => 'Satuan berhasil dihapus dari kategori',
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus satuan dari kategori',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
