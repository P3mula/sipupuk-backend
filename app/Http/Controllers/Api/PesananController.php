<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PesananController extends Controller
{
    /**
     * Create pesanan (checkout) - MOBILE (Pembeli)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'toko_id' => 'required|exists:tokos,id',
            'items' => 'required|array|min:1',
            'items.*.produk_id' => 'required|exists:produks,id',
            'items.*.jumlah' => 'required|integer|min:1',
            'catatan_pembeli' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $user = $request->user();
            $tokoId = $request->input('toko_id');
            $items = $request->input('items');
            $totalHarga = 0;
            $totalItem = 0;
            $detailPesananData = [];

            // Validasi semua produk
            foreach ($items as $item) {
                $produk = Produk::with(['kategoriProduk', 'satuan'])->find($item['produk_id']);

                if (!$produk) {
                    throw new \Exception("Produk tidak ditemukan");
                }

                // Check toko_id consistency
                if ($produk->toko_id != $tokoId) {
                    throw new \Exception("Semua produk harus dari toko yang sama");
                }

                // Check stok
                if ($produk->stok < $item['jumlah']) {
                    throw new \Exception("Stok produk {$produk->nama_produk} tidak mencukupi");
                }

                // Check status produk
                if ($produk->status !== 'tersedia') {
                    throw new \Exception("Produk {$produk->nama_produk} tidak tersedia");
                }

                // Calculate subtotal
                $subtotal = $produk->harga * $item['jumlah'];
                $totalHarga += $subtotal;
                $totalItem += $item['jumlah'];

                // Prepare detail pesanan data
                $detailPesananData[] = [
                    'produk' => $produk,
                    'jumlah' => $item['jumlah'],
                    'subtotal' => $subtotal
                ];
            }

            // Create pesanan
            $pesanan = Pesanan::create([
                'kode_pesanan' => Pesanan::generateKodePesanan(),
                'user_id' => $user->id,
                'toko_id' => $tokoId,
                'total_harga' => $totalHarga,
                'total_item' => $totalItem,
                'status' => 'pending',
                'catatan_pembeli' => $request->input('catatan_pembeli')
            ]);

            // Create detail pesanan and update stok
            foreach ($detailPesananData as $detail) {
                $produk = $detail['produk'];

                DetailPesanan::create([
                    'pesanan_id' => $pesanan->id,
                    'produk_id' => $produk->id,
                    'nama_produk' => $produk->nama_produk,
                    'merk' => $produk->merk,
                    'kategori_produk' => $produk->kategoriProduk->nama_kategori ?? null, // ✅ Simpan nama kategori
                    'harga' => $produk->harga,
                    'jumlah' => $detail['jumlah'],
                    'satuan' => $produk->satuan->nama_satuan , // ✅ Simpan nama satuan
                    'subtotal' => $detail['subtotal']
                ]);

                // Update stok
                $produk->decrement('stok', $detail['jumlah']);

                // Update status if stok habis
                if ($produk->stok <= 0) {
                    $produk->update(['status' => 'habis']);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibuat',
                'data' => $pesanan->load(['toko', 'detailPesanans.produk'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get pesanan pembeli - MOBILE (Pembeli)
     */
    public function myPesanan(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $status = $request->input('status');

        $query = Pesanan::where('user_id', $request->user()->id)
            ->with(['toko', 'detailPesanans.produk']);

        if ($status) {
            $query->where('status', $status);
        }

        $pesanans = $query->latest()->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Data pesanan berhasil diambil',
            'data' => $pesanans
        ], 200);
    }

    /**
     * Get pesanan toko - MOBILE (Penjual)
     */
    public function tokoPesanan(Request $request)
    {
        $user = $request->user();
        
        // ✅ Query langsung tanpa relasi
        $toko = \App\Models\Toko::where('user_id', $user->id)->first();

        if (!$toko) {
            return response()->json([
                'success' => false,
                'message' => 'Toko tidak ditemukan'
            ], 404);
        }

        $perPage = $request->input('per_page', 10);
        $status = $request->input('status');

        $query = Pesanan::where('toko_id', $toko->id)
            ->with(['user', 'detailPesanans.produk']);

        if ($status) {
            $query->where('status', $status);
        }

        $pesanans = $query->latest()->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Data pesanan berhasil diambil',
            'data' => $pesanans
        ], 200);
    }

    /**
     * Get single pesanan detail
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $pesanan = Pesanan::with(['user', 'toko', 'detailPesanans.produk'])->find($id);

        if (!$pesanan) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan'
            ], 404);
        }

        // Check authorization - Pembeli
        if ($user->role === 'pembeli') {
            if ($pesanan->user_id != $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized - Bukan pesanan Anda'
                ], 403);
            }
        }

        // Check authorization - Penjual
        if ($user->role === 'penjual') {
            // ✅ Query langsung ke database
            $toko = \App\Models\Toko::where('user_id', $user->id)->first();
            
            if (!$toko) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki toko'
                ], 403);
            }
            
            if ($pesanan->toko_id != $toko->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized - Bukan pesanan toko Anda'
                ], 403);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Data pesanan berhasil diambil',
            'data' => $pesanan
        ], 200);
    }

    /**
     * Cancel pesanan - MOBILE (Pembeli)
     */
    public function cancel(Request $request, $id)
    {
        $pesanan = Pesanan::where('user_id', $request->user()->id)->find($id);

        if (!$pesanan) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan'
            ], 404);
        }

        if ($pesanan->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya pesanan dengan status pending yang dapat dibatalkan'
            ], 400);
        }

        DB::beginTransaction();

        try {
            // Return stok
            foreach ($pesanan->detailPesanans as $detail) {
                $produk = $detail->produk;
                if ($produk) {
                    $produk->increment('stok', $detail->jumlah);
                    
                    // Update status if was habis
                    if ($produk->status === 'habis' && $produk->stok > 0) {
                        $produk->update(['status' => 'tersedia']);
                    }
                }
            }

            $pesanan->update(['status' => 'dibatalkan']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibatalkan',
                'data' => $pesanan
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan pesanan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Konfirmasi pesanan - MOBILE (Penjual)
     */
    public function konfirmasi(Request $request, $id)
    {
        $user = $request->user();
        
        // ✅ Query langsung
        $toko = \App\Models\Toko::where('user_id', $user->id)->first();

        if (!$toko) {
            return response()->json([
                'success' => false,
                'message' => 'Toko tidak ditemukan'
            ], 404);
        }

        $pesanan = Pesanan::where('toko_id', $toko->id)->find($id);

        if (!$pesanan) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan'
            ], 404);
        }

        if ($pesanan->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan sudah diproses'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'catatan_toko' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $pesanan->update([
            'status' => 'dikonfirmasi',
            'catatan_toko' => $request->input('catatan_toko'),
            'tanggal_konfirmasi' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pesanan berhasil dikonfirmasi',
            'data' => $pesanan->load(['user', 'detailPesanans.produk'])
        ], 200);
    }

    /**
     * Set pesanan siap diambil - MOBILE (Penjual)
     */
    public function siapDiambil(Request $request, $id)
    {
        $user = $request->user();
        
        // ✅ Query langsung
        $toko = \App\Models\Toko::where('user_id', $user->id)->first();

        if (!$toko) {
            return response()->json([
                'success' => false,
                'message' => 'Toko tidak ditemukan'
            ], 404);
        }

        $pesanan = Pesanan::where('toko_id', $toko->id)->find($id);

        if (!$pesanan) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan'
            ], 404);
        }

        if ($pesanan->status !== 'dikonfirmasi') {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan belum dikonfirmasi'
            ], 400);
        }

        $pesanan->update([
            'status' => 'siap_diambil',
            'tanggal_siap' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pesanan siap diambil',
            'data' => $pesanan->load(['user', 'detailPesanans.produk'])
        ], 200);
    }

    /**
     * Selesaikan pesanan (sudah dibayar & diambil) - MOBILE (Penjual)
     */
    public function selesai(Request $request, $id)
    {
        $user = $request->user();
        
        // ✅ Query langsung
        $toko = \App\Models\Toko::where('user_id', $user->id)->first();

        if (!$toko) {
            return response()->json([
                'success' => false,
                'message' => 'Toko tidak ditemukan'
            ], 404);
        }

        $pesanan = Pesanan::where('toko_id', $toko->id)->find($id);

        if (!$pesanan) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan'
            ], 404);
        }

        if ($pesanan->status !== 'siap_diambil') {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan belum siap diambil'
            ], 400);
        }

        $pesanan->update([
            'status' => 'selesai',
            'tanggal_selesai' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pesanan selesai',
            'data' => $pesanan->load(['user', 'detailPesanans.produk'])
        ], 200);
    }

    /**
     * Tolak pesanan - MOBILE (Penjual)
     */
    public function tolak(Request $request, $id)
    {
        $user = $request->user();
        
        // ✅ Query langsung
        $toko = \App\Models\Toko::where('user_id', $user->id)->first();

        if (!$toko) {
            return response()->json([
                'success' => false,
                'message' => 'Toko tidak ditemukan'
            ], 404);
        }

        $pesanan = Pesanan::where('toko_id', $toko->id)->find($id);

        if (!$pesanan) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan'
            ], 404);
        }

        if ($pesanan->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya pesanan pending yang dapat ditolak'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'catatan_toko' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Return stok
            foreach ($pesanan->detailPesanans as $detail) {
                $produk = $detail->produk;
                if ($produk) {
                    $produk->increment('stok', $detail->jumlah);
                    
                    // Update status if was habis
                    if ($produk->status === 'habis' && $produk->stok > 0) {
                        $produk->update(['status' => 'tersedia']);
                    }
                }
            }

            $pesanan->update([
                'status' => 'dibatalkan',
                'catatan_toko' => $request->input('catatan_toko')
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pesanan ditolak',
                'data' => $pesanan->load(['user', 'detailPesanans.produk'])
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menolak pesanan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get statistics pesanan - For Dashboard
     */
    public function statistics(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'pembeli') {
            $stats = [
                'total' => Pesanan::where('user_id', $user->id)->count(),
                'pending' => Pesanan::where('user_id', $user->id)->where('status', 'pending')->count(),
                'dikonfirmasi' => Pesanan::where('user_id', $user->id)->where('status', 'dikonfirmasi')->count(),
                'siap_diambil' => Pesanan::where('user_id', $user->id)->where('status', 'siap_diambil')->count(),
                'selesai' => Pesanan::where('user_id', $user->id)->where('status', 'selesai')->count(),
                'dibatalkan' => Pesanan::where('user_id', $user->id)->where('status', 'dibatalkan')->count()
            ];
        } elseif ($user->role === 'penjual') {
            // ✅ Query langsung
            $toko = \App\Models\Toko::where('user_id', $user->id)->first();
            
            if (!$toko) {
                return response()->json([
                    'success' => false,
                    'message' => 'Toko tidak ditemukan'
                ], 404);
            }
            
            $stats = [
                'total' => Pesanan::where('toko_id', $toko->id)->count(),
                'pending' => Pesanan::where('toko_id', $toko->id)->where('status', 'pending')->count(),
                'dikonfirmasi' => Pesanan::where('toko_id', $toko->id)->where('status', 'dikonfirmasi')->count(),
                'siap_diambil' => Pesanan::where('toko_id', $toko->id)->where('status', 'siap_diambil')->count(),
                'selesai' => Pesanan::where('toko_id', $toko->id)->where('status', 'selesai')->count(),
                'dibatalkan' => Pesanan::where('toko_id', $toko->id)->where('status', 'dibatalkan')->count()
            ];
        } else {
            // Admin
            $stats = [
                'total' => Pesanan::count(),
                'pending' => Pesanan::where('status', 'pending')->count(),
                'dikonfirmasi' => Pesanan::where('status', 'dikonfirmasi')->count(),
                'siap_diambil' => Pesanan::where('status', 'siap_diambil')->count(),
                'selesai' => Pesanan::where('status', 'selesai')->count(),
                'dibatalkan' => Pesanan::where('status', 'dibatalkan')->count()
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Statistik pesanan berhasil diambil',
            'data' => $stats
        ], 200);
    }
}