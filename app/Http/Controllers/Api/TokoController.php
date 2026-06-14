<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Toko;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class TokoController extends Controller
{
    /**
     * Get toko milik penjual yang sedang login
     */
    public function myToko(Request $request)
    {
        $toko = Toko::where('user_id', $request->user()->id)
            ->with('user')
            ->first();

        if (!$toko) {
            return response()->json([
                'success' => false,
                'message' => 'Toko belum dibuat'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data toko berhasil diambil',
            'data' => $toko
        ], 200);
    }

    /**
     * Create toko (hanya jika belum punya toko)
     */
    public function store(Request $request)
    {
        // Check if user already has toko
        $existingToko = Toko::where('user_id', $request->user()->id)->first();
        
        if ($existingToko) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah memiliki toko'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'nama_toko' => 'required|string|max:255',
            'alamat' => 'required|string',
            'desa' => 'required|string|max:100',
            'no_telepon' => 'required|string|max:15',
            'deskripsi' => 'nullable|string',
            // 'foto_toko' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'jam_buka' => 'nullable|date_format:H:i',
            'jam_tutup' => 'nullable|date_format:H:i'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->all();
        $data['user_id'] = $request->user()->id;

        // Handle foto upload
        // if ($request->hasFile('foto_toko')) {
        //     $file = $request->file('foto_toko');
        //     $filename = time() . '_' . $file->getClientOriginalName();
        //     $path = $file->storeAs('toko', $filename, 'public');
        //     $data['foto_toko'] = $path;
        // }

        $toko = Toko::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Toko berhasil dibuat',
            'data' => $toko
        ], 201);
    }

    /**
     * Update toko
     */
    public function update(Request $request)
    {
        $toko = Toko::where('user_id', $request->user()->id)->first();

        if (!$toko) {
            return response()->json([
                'success' => false,
                'message' => 'Toko tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama_toko' => 'required|string|max:255',
            'alamat' => 'required|string',
            'desa' => 'required|string|max:100',
            'no_telepon' => 'required|string|max:15',
            'deskripsi' => 'nullable|string',
            // 'foto_toko' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'jam_buka' => 'nullable|date_format:H:i',
            'jam_tutup' => 'nullable|date_format:H:i',
            'status' => 'nullable|in:aktif,nonaktif'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->all();

        // Handle foto upload
        // if ($request->hasFile('foto_toko')) {
        //     // Delete old photo
        //     if ($toko->foto_toko) {
        //         Storage::disk('public')->delete($toko->foto_toko);
        //     }

        //     $file = $request->file('foto_toko');
        //     $filename = time() . '_' . $file->getClientOriginalName();
        //     $path = $file->storeAs('toko', $filename, 'public');
        //     $data['foto_toko'] = $path;
        // }

        $toko->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Toko berhasil diupdate',
            'data' => $toko
        ], 200);
    }

    /**
     * Get all tokos (for admin or public)
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $status = $request->input('status');
        $search = $request->input('search');

        $query = Toko::with('user');

        if ($status) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_toko', 'like', "%{$search}%")
                  ->orWhere('desa', 'like', "%{$search}%")
                  ->orWhere('kecamatan', 'like', "%{$search}%");
            });
        }

        $tokos = $query->latest()->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Data toko berhasil diambil',
            'data' => $tokos
        ], 200);
    }

    /**
     * Get single toko by id (public)
     */
    public function show($id)
    {
        $toko = Toko::with(['user', 'produks'])->find($id);

        if (!$toko) {
            return response()->json([
                'success' => false,
                'message' => 'Toko tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data toko berhasil diambil',
            'data' => $toko
        ], 200);
    }

     /**
     * Get list semua toko (untuk pembeli pilih toko)
     */
    // public function list(Request $request)
    // {
    //     $tokos = Toko::select('id', 'nama_toko', 'alamat', 'desa', 'no_telepon', 'foto_toko')
    //         ->where('status', 'aktif') // Jika ada field status
    //         ->orderBy('nama_toko', 'asc')
    //         ->get();

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Data toko berhasil diambil',
    //         'data' => $tokos
    //     ], 200);
    // }
}