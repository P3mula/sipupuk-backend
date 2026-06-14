@extends('admin.layout')

@section('title', 'Mapping Kategori-Satuan')
@section('page-title', 'Mapping Kategori-Satuan')

@section('content')
<div class="mb-6">
    <h3 class="text-xl font-semibold text-gray-800">Atur Satuan untuk Setiap Kategori</h3>
    <p class="text-gray-500 mt-1">Tentukan satuan mana yang tersedia untuk setiap kategori produk</p>
</div>

<div class="space-y-6">
    @foreach($kategoris as $kategori)
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h4 class="text-lg font-semibold text-gray-800">
                @if($kategori->icon)
                <span class="text-2xl mr-2">{{ $kategori->icon }}</span>
                @endif
                {{ $kategori->nama_kategori }}
            </h4>
            <span class="text-sm text-gray-500">
                {{ $kategori->satuans->count() }} satuan tersedia
            </span>
        </div>

        <form action="{{ route('admin.satuan.mapping.update', $kategori) }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 mb-4">
                @foreach($allSatuans as $satuan)
                <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                    <input 
                        type="checkbox" 
                        name="satuan_ids[]" 
                        value="{{ $satuan->id }}"
                        {{ $kategori->satuans->contains($satuan->id) ? 'checked' : '' }}
                        class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500"
                    >
                    <span class="ml-2 text-sm text-gray-700">
                        {{ $satuan->nama_satuan }}
                        @if($satuan->singkatan)
                        <span class="text-gray-500">({{ $satuan->singkatan }})</span>
                        @endif
                    </span>
                </label>
                @endforeach
            </div>

            <button 
                type="submit" 
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium"
            >
                <i class="fas fa-save mr-2"></i>
                Simpan untuk {{ $kategori->nama_kategori }}
            </button>
        </form>
    </div>
    @endforeach
</div>

@if($kategoris->isEmpty())
<div class="bg-white rounded-lg shadow-md p-8 text-center">
    <i class="fas fa-inbox text-gray-400 text-5xl mb-4"></i>
    <p class="text-gray-500">Belum ada kategori. Silakan tambah kategori terlebih dahulu.</p>
    <a href="{{ route('admin.kategori.create') }}" class="inline-block mt-4 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
        Tambah Kategori
    </a>
</div>
@endif
@endsection