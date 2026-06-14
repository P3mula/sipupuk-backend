@extends('admin.layout')

@section('title', isset($kategori) ? 'Edit Kategori' : 'Tambah Kategori')
@section('page-title', isset($kategori) ? 'Edit Kategori' : 'Tambah Kategori')

@section('content')
<div class="max-w-2xl">
    <a href="{{ route('admin.kategori.index') }}" class="text-green-600 hover:text-green-700 mb-4 inline-flex items-center">
        <i class="fas fa-arrow-left mr-2"></i>
        Kembali
    </a>

    <div class="bg-white rounded-lg shadow-md p-6 mt-4">
        <form action="{{ isset($kategori) ? route('admin.kategori.update', $kategori) : route('admin.kategori.store') }}" method="POST">
            @csrf
            @if(isset($kategori))
                @method('PUT')
            @endif

            <div class="mb-4">
                <label for="nama_kategori" class="block text-sm font-medium text-gray-700 mb-2">
                    Nama Kategori <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="nama_kategori" 
                    id="nama_kategori" 
                    value="{{ old('nama_kategori', $kategori->nama_kategori ?? '') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('nama_kategori') border-red-500 @enderror"
                    placeholder="Contoh: Pupuk, Bibit, Pestisida"
                    required
                >
                @error('nama_kategori')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="icon" class="block text-sm font-medium text-gray-700 mb-2">
                    Icon Emoji
                </label>
                <input 
                    type="text" 
                    name="icon" 
                    id="icon" 
                    value="{{ old('icon', $kategori->icon ?? '') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('icon') border-red-500 @enderror"
                    placeholder="🌱"
                    maxlength="10"
                >
                <p class="mt-1 text-sm text-gray-500">
                    Gunakan emoji sebagai icon. Contoh: 🌱 🌾 🧪 🔧
                </p>
                @error('icon')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">
                    Deskripsi
                </label>
                <textarea 
                    name="deskripsi" 
                    id="deskripsi" 
                    rows="3"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('deskripsi') border-red-500 @enderror"
                    placeholder="Deskripsi singkat tentang kategori ini"
                >{{ old('deskripsi', $kategori->deskripsi ?? '') }}</textarea>
                @error('deskripsi')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="flex items-center">
                    <input 
                        type="checkbox" 
                        name="is_active" 
                        value="1"
                        {{ old('is_active', $kategori->is_active ?? true) ? 'checked' : '' }}
                        class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500"
                    >
                    <span class="ml-2 text-sm text-gray-700">Aktif</span>
                </label>
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
                    href="{{ route('admin.kategori.index') }}" 
                    class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg font-medium"
                >
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection