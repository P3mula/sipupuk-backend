@extends('admin.layout')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Users -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Total Pengguna</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['total_users'] }}</p>
                <p class="text-sm text-gray-400 mt-1">
                    <span class="text-blue-600">{{ $stats['total_pembeli'] }}</span> Pembeli · 
                    <span class="text-green-600">{{ $stats['total_penjual'] }}</span> Penjual
                </p>
            </div>
            <div class="bg-blue-100 rounded-full p-4">
                <i class="fas fa-users text-blue-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Total Toko -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Total Toko</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['total_toko'] }}</p>
            </div>
            <div class="bg-green-100 rounded-full p-4">
                <i class="fas fa-store text-green-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Total Produk -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Total Produk</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['total_produk'] }}</p>
                <p class="text-sm text-gray-400 mt-1">
                    <span class="text-green-600">{{ $stats['produk_tersedia'] }}</span> Tersedia · 
                    <span class="text-red-600">{{ $stats['produk_habis'] }}</span> Habis
                </p>
            </div>
            <div class="bg-purple-100 rounded-full p-4">
                <i class="fas fa-box text-purple-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Total Kategori & Satuan -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Kategori & Satuan</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['total_kategori'] + $stats['total_satuan'] }}</p>
                <p class="text-sm text-gray-400 mt-1">
                    <span class="text-orange-600">{{ $stats['total_kategori'] }}</span> Kategori · 
                    <span class="text-teal-600">{{ $stats['total_satuan'] }}</span> Satuan
                </p>
            </div>
            <div class="bg-orange-100 rounded-full p-4">
                <i class="fas fa-tags text-orange-600 text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Latest Products -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Produk Terbaru</h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @forelse($latest_products as $product)
                <div class="flex items-center justify-between py-3 border-b border-gray-100">
                    <div class="flex-1">
                        <p class="font-medium text-gray-800">{{ $product->nama_produk }}</p>
                        <p class="text-sm text-gray-500">
                            {{ $product->toko->nama_toko }} · 
                            <span class="text-green-600">Rp {{ number_format($product->harga, 0, ',', '.') }}</span>
                        </p>
                    </div>
                    <span class="px-3 py-1 text-xs font-medium rounded-full {{ $product->status === 'tersedia' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ ucfirst($product->status) }}
                    </span>
                </div>
                @empty
                <p class="text-gray-500 text-center py-4">Belum ada produk</p>
                @endforelse
            </div>
            @if($latest_products->count() > 0)
            <a href="{{ route('admin.produk.index') }}" class="block text-center text-green-600 hover:text-green-700 font-medium mt-4">
                Lihat Semua Produk →
            </a>
            @endif
        </div>
    </div>

    <!-- Latest Users -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Pengguna Terbaru</h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @forelse($latest_users as $user)
                <div class="flex items-center justify-between py-3 border-b border-gray-100">
                    <div>
                        <p class="font-medium text-gray-800">{{ $user->name }}</p>
                        <p class="text-sm text-gray-500">{{ $user->phone }}</p>
                    </div>
                    <span class="px-3 py-1 text-xs font-medium rounded-full {{ $user->role === 'penjual' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                        {{ ucfirst($user->role) }}
                    </span>
                </div>
                @empty
                <p class="text-gray-500 text-center py-4">Belum ada pengguna</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection