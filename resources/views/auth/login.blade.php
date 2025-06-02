@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center animate-gradient-animate">
    <div class="bg-black/40 p-10 rounded-xl shadow-lg w-full max-w-md border border-gray-700 backdrop-blur-sm">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-extrabold text-teal-400 tracking-wider">SISFO SARPRAS</h1>
            <p class="text-teal-300 mt-2 text-sm">Masuk ke sistem informasi sarana prasarana</p>
        </div>

        <form method="POST" action="{{ route('auth.login') }}">
            @csrf

            {{-- Username --}}
            <div class="mb-5">
                <label for="username" class="block text-teal-400 font-medium mb-2">Username</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-teal-300" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        class="w-full bg-gray-800 bg-opacity-50 border border-gray-700 px-4 py-3 pl-10 rounded-full text-teal-200 placeholder-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors"
                        value="{{ old('username') }}"
                        placeholder="Masukkan username Anda"
                        required
                    >
                </div>
            </div>

            {{-- Password --}}
            <div class="mb-6">
                <label for="password" class="block text-teal-400 font-medium mb-2">Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-teal-300" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="w-full bg-gray-800 bg-opacity-50 border border-gray-700 px-4 py-3 pl-10 rounded-full text-teal-200 placeholder-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors"
                        placeholder="Masukkan password Anda"
                        required
                    >
                    <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <i class="fas fa-eye text-teal-300"></i>
                    </button>
                </div>
            </div>

            {{-- Error Message --}}
            @if ($errors->any())
                <div class="mb-4 bg-red-600 bg-opacity-10 p-3 rounded-lg border border-red-600">
                    <p class="text-red-400 text-sm flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        {{ $errors->first() }}
                    </p>
                </div>
            @endif

            {{-- Submit --}}
            <div class="mb-6">
                <button
                    type="submit"
                    class="w-full bg-gradient-to-r from-teal-600 to-teal-500 hover:bg-gradient-to-l text-white py-3 px-4 rounded-full font-semibold shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1"
                >
                    Masuk
                </button>
            </div>

            <div class="text-center text-xs text-teal-500">
                <p>Sistem Informasi Sarana Prasarana</p>
                <p class="mt-1">&copy; {{ date('Y') }}</p>
            </div>
        </form>
    </div>
</div>

{{-- Gradient Animation --}}
@push('scripts')
<style>
    @keyframes diagonalGradientBG {
        0% { background-position: 0% 0%; }
        50% { background-position: 100% 100%; }
        100% { background-position: 0% 0%; }
    }

    .animate-gradient-animate {
        background: linear-gradient(135deg, #000000, #14b8a6, #000000);
        background-size: 400% 400%;
        animation: diagonalGradientBG 15s ease infinite;
    }
</style>

@if (session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Login Berhasil!',
            text: '{{ session('success') }}',
            confirmButtonText: 'OK',
            background: '#333',
            color: '#fff'
        });
    </script>
@endif

@if (session('error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Login Gagal',
            text: '{{ session('error') }}',
            confirmButtonText: 'Coba Lagi',
            background: '#333',
            color: '#fff'
        });
    </script>
@endif
@endpush

{{-- Toggle Password --}}
<script>
    const togglePassword = document.getElementById('togglePassword');
    const passwordField = document.getElementById('password');

    togglePassword.addEventListener('click', function () {
        const type = passwordField.type === 'password' ? 'text' : 'password';
        passwordField.type = type;
        togglePassword.querySelector('i').classList.toggle('fa-eye');
        togglePassword.querySelector('i').classList.toggle('fa-eye-slash');
    });
</script>
@endsection
