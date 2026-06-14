<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanController extends Controller
{
    /**
     * Laporan penjualan bulan ini untuk penjual
     */
    public function laporanBulanan(Request $request)
    {
        try {
            // Ambil toko milik penjual yang login
            $toko = $request->user()->toko;

            if (!$toko) {
                return response()->json([
                    'success' => false,
                    'message' => 'Toko tidak ditemukan'
                ], 404);
            }

            // Tanggal awal dan akhir bulan ini
            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now()->endOfMonth();

            // ========== RINGKASAN LAPORAN ==========
            
            // Total pendapatan bulan ini (hanya pesanan selesai)
            $totalPendapatan = Pesanan::where('toko_id', $toko->id)
                ->where('status', 'selesai')
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->sum('total_harga');

            // Total pesanan bulan ini (semua status)
            $totalPesanan = Pesanan::where('toko_id', $toko->id)
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->count();

            // Pesanan selesai
            $pesananSelesai = Pesanan::where('toko_id', $toko->id)
                ->where('status', 'selesai')
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->count();

            // Total item terjual (dari detail pesanan yang statusnya selesai)
            $totalItemTerjual = DetailPesanan::whereHas('pesanan', function ($query) use ($toko, $startOfMonth, $endOfMonth) {
                    $query->where('toko_id', $toko->id)
                        ->where('status', 'selesai')
                        ->whereBetween('created_at', [$startOfMonth, $endOfMonth]);
                })
                ->sum('jumlah');

            // Pesanan dibatalkan
            $pesananDibatalkan = Pesanan::where('toko_id', $toko->id)
                ->where('status', 'dibatalkan')
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->count();

            // ========== DETAIL TRANSAKSI ==========
            
            $transaksi = Pesanan::where('toko_id', $toko->id)
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->with(['user', 'detailPesanans'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($pesanan) {
                    return [
                        'id' => $pesanan->id,
                        'kodePesanan' => $pesanan->kode_pesanan,
                        'namaPembeli' => $pesanan->user->name ?? 'Unknown',
                        'tanggal' => $pesanan->created_at->format('d M Y, H:i'),
                        'totalHarga' => $pesanan->total_harga,
                        'totalItem' => $pesanan->total_item,
                        'status' => $pesanan->status,
                        'catatanPembeli' => $pesanan->catatan_pembeli,
                    ];
                });

            // Response
            return response()->json([
                'success' => true,
                'message' => 'Laporan berhasil diambil',
                'data' => [
                    'periode' => [
                        'bulan' => Carbon::now()->format('F Y'),
                        'startDate' => $startOfMonth->toDateString(),
                        'endDate' => $endOfMonth->toDateString(),
                    ],
                    'ringkasan' => [
                        'totalPendapatan' => (float) $totalPendapatan,
                        'totalPesanan' => $totalPesanan,
                        'pesananSelesai' => $pesananSelesai,
                        'totalItemTerjual' => (int) $totalItemTerjual,
                        'pesananDibatalkan' => $pesananDibatalkan,
                    ],
                    'transaksi' => $transaksi,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil laporan',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}