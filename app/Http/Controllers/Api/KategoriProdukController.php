<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\KategoriProduk;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class KategoriProdukController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $kategoriList = KategoriProduk::where('is_active', true)
                ->orderBy('nama_kategori', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Kategori produk berhasil diambil',
                'data' => $kategoriList,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil kategori produk',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $kategori = KategoriProduk::withCount('produks')->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Detail kategori produk berhasil diambil',
                'data' => $kategori,
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori produk tidak ditemukan',
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail kategori produk',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'nama_kategori' => 'required|string|max:100|unique:kategori_produks,nama_kategori',
                'icon' => 'nullable|string|max:10',
                'deskripsi' => 'nullable|string|max:500',
                'is_active' => 'boolean',
            ]);

            $kategori = KategoriProduk::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Kategori produk berhasil dibuat',
                'data' => $kategori,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat kategori produk',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $kategori = KategoriProduk::findOrFail($id);

            $validated = $request->validate([
                'nama_kategori' => 'sometimes|string|max:100|unique:kategori_produks,nama_kategori,' . $id,
                'icon' => 'nullable|string|max:10',
                'deskripsi' => 'nullable|string|max:500',
                'is_active' => 'sometimes|boolean',
            ]);

            $kategori->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Kategori produk berhasil diupdate',
                'data' => $kategori,
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori produk tidak ditemukan',
            ], 404);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate kategori produk',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $kategori = KategoriProduk::findOrFail($id);

            if ($kategori->produks()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kategori tidak dapat dihapus karena masih digunakan oleh produk',
                    'jumlahProduk' => $kategori->produks()->count(),
                ], 409);
            }

            $kategori->delete();

            return response()->json([
                'success' => true,
                'message' => 'Kategori produk berhasil dihapus',
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori produk tidak ditemukan',
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus kategori produk',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}