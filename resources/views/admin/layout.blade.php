<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - SI PUPUK</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @stack('styles')
</head>
<body class="bg-gray-100">
    
    @auth
    <!-- Sidebar -->
    <div class="fixed inset-y-0 left-0 w-64 bg-green-800 text-white z-50">
        <div class="flex items-center justify-center h-16 bg-green-900">
            <h1 class="text-2xl font-bold">🌾 SI PUPUK</h1>
        </div>
        
        <nav class="mt-8">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('admin.dashboard') ? 'bg-green-900 border-l-4 border-white' : 'hover:bg-green-700' }}">
                <i class="fas fa-chart-line mr-3"></i>
                Dashboard
            </a>
            
            <a href="{{ route('admin.kategori.index') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('admin.kategori.*') ? 'bg-green-900 border-l-4 border-white' : 'hover:bg-green-700' }}">
                <i class="fas fa-tags mr-3"></i>
                Kategori Produk
            </a>
            
            <a href="{{ route('admin.satuan.index') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('admin.satuan.*') ? 'bg-green-900 border-l-4 border-white' : 'hover:bg-green-700' }}">
                <i class="fas fa-weight mr-3"></i>
                Satuan
            </a>
            
            <a href="{{ route('admin.satuan.mapping') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('admin.satuan.mapping*') ? 'bg-green-900 border-l-4 border-white' : 'hover:bg-green-700' }}">
                <i class="fas fa-link mr-3"></i>
                Mapping Kategori-Satuan
            </a>
            
            <a href="{{ route('admin.produk.index') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('admin.produk.*') ? 'bg-green-900 border-l-4 border-white' : 'hover:bg-green-700' }}">
                <i class="fas fa-box mr-3"></i>
                Kelola Produk
            </a>
            
            <form action="{{ route('admin.logout') }}" method="POST" class="mt-8">
                @csrf
                <button type="submit" class="flex items-center px-6 py-3 w-full hover:bg-red-700 text-left">
                    <i class="fas fa-sign-out-alt mr-3"></i>
                    Logout
                </button>
            </form>
        </nav>
    </div>
    @endauth

    <!-- Main Content -->
    <div class="@auth ml-64 @endauth">
        
        @auth
        <!-- Top Bar -->
        <div class="bg-white shadow-sm">
            <div class="flex items-center justify-between px-8 py-4">
                <h2 class="text-2xl font-semibold text-gray-800">
                    @yield('page-title', 'Dashboard')
                </h2>
                
                <div class="flex items-center space-x-4">
                    <span class="text-gray-600">
                        <i class="fas fa-user-circle mr-2"></i>
                        {{ Auth::user()->name }}
                    </span>
                </div>
            </div>
        </div>
        @endauth

        <!-- Flash Messages -->
        @if(session('success'))
        <div class="mx-8 mt-4">
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded" role="alert">
                <p class="font-medium">{{ session('success') }}</p>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="mx-8 mt-4">
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded" role="alert">
                <p class="font-medium">{{ session('error') }}</p>
            </div>
        </div>
        @endif

        @if($errors->any())
        <div class="mx-8 mt-4">
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded" role="alert">
                <p class="font-medium">Terjadi kesalahan:</p>
                <ul class="mt-2 list-disc list-inside">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        <!-- Page Content -->
        <div class="p-8">
            @yield('content')
        </div>
    </div>

    @stack('scripts')
</body>
</html>