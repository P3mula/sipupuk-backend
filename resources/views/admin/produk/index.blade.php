@extends('admin.layout')

@section('title', 'Kelola Produk')
@section('page-title', 'Kelola Produk')

@section('content')
<div class="mb-6">
    <h3 class="text-xl font-semibold text-gray-800">Daftar Semua Produk</h3>
    <p class="text-gray-500 mt-1">Monitor dan kelola produk dari semua toko</p>
</div>

<!-- Filter -->
<div class="bg-white rounded-lg shadow-md p-4 mb-6">
    <form action="{{ route('admin.produk.index') }}" method="GET" class="flex flex-wrap gap-4">
        <div class="flex-1 min-w-[200px]">
            <input 
                type="text" 
                name="search" 
                value="{{ request('search') }}"
                placeholder="Cari produk..." 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
            >
        </div>
        <div class="w-48">
            <select name="kategori_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                <option value="">Semua Kategori</option>
                @foreach($kategoris as $kategori)
                <option value="{{ $kategori->id }}" {{ request('kategori_id') == $kategori->id ? 'selected' : '' }}>
                    {{ $kategori->nama_kategori }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="w-40">
            <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                <option value="">Semua Status</option>
                <option value="tersedia" {{ request('status') == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                <option value="habis" {{ request('status') == 'habis' ? 'selected' : '' }}>Habis</option>
            </select>
        </div>
        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg">
            <i class="fas fa-search mr-2"></i>
            Filter
        </button>
        @if(request()->hasAny(['search', 'kategori_id', 'status']))
        <a href="{{ route('admin.produk.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg">
            Reset
        </a>
        @endif
    </form>
</div>

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Toko</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($produks as $produk)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4">
                    <div class="flex items-center">
                        @if($produk->foto_produk_url)
                        <img src="{{ $produk->foto_produk_url }}" alt="{{ $produk->nama_produk }}" class="w-12 h-12 rounded object-cover mr-3">
                        @else
                        <div class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center mr-3">
                            <i class="fas fa-box text-gray-400"></i>
                        </div>
                        @endif
                        <div>
                            <div class="text-sm font-medium text-gray-900">{{ $produk->nama_produk }}</div>
                            <div class="text-sm text-gray-500">{{ $produk->merk }}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ $produk->toko->nama_toko }}</div>
                    <div class="text-sm text-gray-500">{{ $produk->toko->user->name }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-800">
                        @if($produk->kategoriProduk->icon)
                        {{ $produk->kategoriProduk->icon }}
                        @endif
                        {{ $produk->kategoriProduk->nama_kategori }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">Rp {{ number_format($produk->harga, 0, ',', '.') }}</div>
                    <div class="text-sm text-gray-500">per {{ $produk->satuan->nama_satuan }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ $produk->stok }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <form action="{{ route('admin.produk.toggle-status', $produk) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-3 py-1 text-xs font-semibold rounded-full {{ $produk->status === 'tersedia' ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-red-100 text-red-700 hover:bg-red-200' }}">
                            {{ ucfirst($produk->status) }}
                        </button>
                    </form>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                    {{-- <a href="{{ route('admin.produk.show', $produk) }}" class="text-blue-600 hover:text-blue-900">
                        <i class="fas fa-eye"></i>
                    </a> --}}
                    <form action="{{ route('admin.produk.destroy', $produk) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus produk ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                    <i class="fas fa-inbox text-4xl mb-2"></i>
                    <p>Tidak ada produk ditemukan</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $produks->appends(request()->query())->links() }}
</div>
@endsection