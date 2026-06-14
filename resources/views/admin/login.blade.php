@extends('admin.layout')

@section('title', 'Login Admin')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-green-600 to-green-800 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full">
        <div class="bg-white rounded-lg shadow-xl p-8">
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-green-800 mb-2">🌾 SI PUPUK</h1>
                <h2 class="text-2xl font-semibold text-gray-700">Admin Panel</h2>
                <p class="text-gray-500 mt-2">Silakan login untuk melanjutkan</p>
            </div>

            <form action="{{ route('admin.login.submit') }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                        Nomor Telepon
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-phone text-gray-400"></i>
                        </div>
                        <input 
                            type="text" 
                            name="phone" 
                            id="phone" 
                            value="{{ old('phone') }}"
                            class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('phone') border-red-500 @enderror" 
                            placeholder="081234567890"
                            required
                            autofocus
                        >
                    </div>
                    @error('phone')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input 
                            type="password" 
                            name="password" 
                            id="password" 
                            class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('password') border-red-500 @enderror" 
                            placeholder="••••••••"
                            required
                        >
                    </div>
                    @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button 
                    type="submit" 
                    class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center"
                >
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Login
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-500">
                    Default: 081234567890 / admin123
                </p>
            </div>
        </div>
    </div>
</div>
@endsection