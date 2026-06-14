@extends('admin.layout')

@section('title', 'Kelola Satuan')
@section('page-title', 'Kelola Satuan')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h3 class="text-xl font-semibold text-gray-800">Daftar Satuan</h3>
        <p class="text-gray-500 mt-1">Kelola satuan produk (kg, karung, liter, dll)</p>
    </div>
    <a href="{{ route('admin.satuan.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium flex items-center">
        <i class="fas fa-plus mr-2"></i>
        Tambah Satuan
    </a>
</div>

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Satuan</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Singkatan</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Produk</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($satuans as $satuan)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ $satuan->nama_satuan }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-500">{{ $satuan->singkatan ?? '-' }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                        {{ $satuan->produks_count }} produk
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                    <a href="{{ route('admin.satuan.edit', $satuan) }}" class="text-blue-600 hover:text-blue-900">
                        <i class="fas fa-edit"></i>
                    </a>
                    @if($satuan->produks_count == 0)
                    <form action="{{ route('admin.satuan.destroy', $satuan) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus satuan ini?')">
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
                <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                    <i class="fas fa-inbox text-4xl mb-2"></i>
                    <p>Belum ada satuan</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $satuans->links() }}
</div>
@endsection