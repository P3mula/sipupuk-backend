@extends('admin.layout')

@section('title', 'Kelola Kategori')
@section('page-title', 'Kelola Kategori Produk')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h3 class="text-xl font-semibold text-gray-800">Daftar Kategori</h3>
        <p class="text-gray-500 mt-1">Kelola kategori produk untuk aplikasi</p>
    </div>
    <a href="{{ route('admin.kategori.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium flex items-center">
        <i class="fas fa-plus mr-2"></i>
        Tambah Kategori
    </a>
</div>

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Produk</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($kategoris as $kategori)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        @if($kategori->icon)
                        <span class="text-2xl mr-3">{{ $kategori->icon }}</span>
                        @endif
                        <div>
                            <div class="text-sm font-medium text-gray-900">{{ $kategori->nama_kategori }}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm text-gray-500">{{ $kategori->deskripsi ?? '-' }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                        {{ $kategori->produks_count }} produk
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <form action="{{ route('admin.kategori.toggle', $kategori) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-3 py-1 text-xs font-semibold rounded-full {{ $kategori->is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            {{ $kategori->is_active ? 'Aktif' : 'Nonaktif' }}
                        </button>
                    </form>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                    <a href="{{ route('admin.kategori.edit', $kategori) }}" class="text-blue-600 hover:text-blue-900">
                        <i class="fas fa-edit"></i>
                    </a>
                    @if($kategori->produks_count == 0)
                    <form action="{{ route('admin.kategori.destroy', $kategori) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                    @else
                    <button disabled class="text-gray-400 cursor-not-allowed" title="Tidak dapat dihapus, masih ada produk">
                        <i class="fas fa-trash"></i>
                    </button>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                    <i class="fas fa-inbox text-4xl mb-2"></i>
                    <p>Belum ada kategori</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $kategoris->links() }}
</div>
@endsection