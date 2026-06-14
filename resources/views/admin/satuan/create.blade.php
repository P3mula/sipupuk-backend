@extends('admin.layout')

@section('title', isset($satuan) ? 'Edit Satuan' : 'Tambah Satuan')
@section('page-title', isset($satuan) ? 'Edit Satuan' : 'Tambah Satuan')

@section('content')
<div class="max-w-2xl">
    <a href="{{ route('admin.satuan.index') }}" class="text-green-600 hover:text-green-700 mb-4 inline-flex items-center">
        <i class="fas fa-arrow-left mr-2"></i>
        Kembali
    </a>

    <div class="bg-white rounded-lg shadow-md p-6 mt-4">
        <form action="{{ isset($satuan) ? route('admin.satuan.update', $satuan) : route('admin.satuan.store') }}" method="POST">
            @csrf
            @if(isset($satuan))
                @method('PUT')
            @endif

            <div class="mb-4">
                <label for="nama_satuan" class="block text-sm font-medium text-gray-700 mb-2">
                    Nama Satuan <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="nama_satuan" 
                    id="nama_satuan" 
                    value="{{ old('nama_satuan', $satuan->nama_satuan ?? '') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('nama_satuan') border-red-500 @enderror"
                    placeholder="Contoh: Kilogram, Karung, Liter"
                    required
                >
                @error('nama_satuan')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="singkatan" class="block text-sm font-medium text-gray-700 mb-2">
                    Singkatan
                </label>
                <input 
                    type="text" 
                    name="singkatan" 
                    id="singkatan" 
                    value="{{ old('singkatan', $satuan->singkatan ?? '') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('singkatan') border-red-500 @enderror"
                    placeholder="Contoh: kg, krg, L"
                    maxlength="10"
                >
                <p class="mt-1 text-sm text-gray-500">
                    Singkatan akan ditampilkan di aplikasi mobile
                </p>
                @error('singkatan')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Kategori yang Cocok
                </label>
                <div class="space-y-2 max-h-64 overflow-y-auto border border-gray-300 rounded-lg p-4">
                    @foreach($kategoris as $kategori)
                    <label class="flex items-center py-1">
                        <input 
                            type="checkbox" 
                            name="kategori_ids[]" 
                            value="{{ $kategori->id }}"
                            {{ in_array($kategori->id, old('kategori_ids', $selectedKategoris ?? [])) ? 'checked' : '' }}
                            class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500"
                        >
                        <span class="ml-2 text-sm text-gray-700">
                            @if($kategori->icon)
                            {{ $kategori->icon }}
                            @endif
                            {{ $kategori->nama_kategori }}
                        </span>
                    </label>
                    @endforeach
                </div>
                <p class="mt-1 text-sm text-gray-500">
                    Pilih kategori dimana satuan ini bisa digunakan
                </p>
            </div>

            <div class="flex space-x-3">
                <button 
                    type="submit" 
                    class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-medium"
                >
                    <i class="fas fa-save mr-2"></i>
                    Simpan
                </button>
                <a 
                    href="{{ route('admin.satuan.index') }}" 
                    class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg font-medium"
                >
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection