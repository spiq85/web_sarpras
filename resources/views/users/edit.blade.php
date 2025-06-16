@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-900 to-black text-white p-6">

    {{-- Header --}}
    <div class="mb-8">
        <h2 class="text-3xl font-semibold">✏️ Edit Pengguna</h2>
    </div>

    {{-- Form Edit --}}
    <section class="bg-gray-800 p-6 rounded-xl shadow-lg">
        <h3 class="text-xl font-medium mb-4 border-b border-gray-700 pb-2">🖊️ Edit Data Pengguna</h3>
        <form action="{{ route('users.update', $user->users_id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Username --}}
            <div class="mb-6">
                <label for="username" class="block text-sm font-semibold text-gray-300">Username</label>
                <input type="text" name="username" id="username" class="w-full bg-gray-700 text-white p-3 rounded-lg shadow-md focus:outline-none focus:ring-2 focus:ring-teal-400"
                    value="{{ $user->username }}" required>
            </div>

            {{-- Name --}}
            <div class="mb-6">
                <label for="name" class="block text-sm font-semibold text-gray-300">Nama Lengkap</label>
                <input type="text" name="name" id="name" class="w-full bg-gray-700 text-white p-3 rounded-lg shadow-md focus:outline-none focus:ring-2 focus:ring-teal-400"
                    value="{{ $user->name }}" required>
            </div>

            {{-- Email --}}
            <div class="mb-6">
                <label for="email" class="block text-sm font-semibold text-gray-300">Email</label>
                <input type="email" name="email" id="email" class="w-full bg-gray-700 text-white p-3 rounded-lg shadow-md focus:outline-none focus:ring-2 focus:ring-teal-400"
                    value="{{ $user->email }}" required>
            </div>

            {{-- Password --}}
            <div class="mb-6">
                <label for="password" class="block text-sm font-semibold text-gray-300">Password Baru (Opsional)</label>
                <input type="password" name="password" id="password" class="w-full bg-gray-700 text-white p-3 rounded-lg shadow-md focus:outline-none focus:ring-2 focus:ring-teal-400">
            </div>

            {{-- Role --}}
            <div class="mb-6">
                <label for="role" class="block text-sm font-semibold text-gray-300">Role</label>
                <select name="role" id="role" class="w-full bg-gray-700 text-white p-3 rounded-lg shadow-md focus:outline-none focus:ring-2 focus:ring-teal-400" required>
                    <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User</option>
                </select>
            </div>

            {{-- Class --}}
            <div class="mb-6">
                <label for="class" class="block text-sm font-semibold text-gray-300">Kelas</label>
                <select name="class" id="class" class="w-full bg-gray-700 text-white p-3 rounded-lg shadow-md focus:outline-none focus:ring-2 focus:ring-teal-400" required>
                    <option value="X" {{ $user->class == 'X' ? 'selected' : '' }}>X</option>
                    <option value="XI" {{ $user->class == 'XI' ? 'selected' : '' }}>XI</option>
                    <option value="XII" {{ $user->class == 'XII' ? 'selected' : '' }}>XII</option>
                </select>
            </div>

            {{-- Major --}}
            <div class="mb-6">
                <label for="major" class="block text-sm font-semibold text-gray-300">Jurusan</label>
                <select name="major" id="major" class="w-full bg-gray-700 text-white p-3 rounded-lg shadow-md focus:outline-none focus:ring-2 focus:ring-teal-400" required>
                    <option value="RPL" {{ $user->major == 'RPL' ? 'selected' : '' }}>RPL</option>
                    <option value="TJKT" {{ $user->major == 'TJKT' ? 'selected' : '' }}>TJKT</option>
                    <option value="PSPT" {{ $user->major == 'PSPT' ? 'selected' : '' }}>PSPT</option>
                    <option value="ANIMASI" {{ $user->major == 'ANIMASI' ? 'selected' : '' }}>ANIMASI</option>
                    <option value="TE" {{ $user->major == 'TE' ? 'selected' : '' }}>TE</option>
                </select>
            </div>

            {{-- Buttons --}}
            <div class="flex justify-between">
                <button type="submit" class="bg-gradient-to-r from-blue-500 to-blue-700 text-white px-6 py-3 rounded-lg shadow-md transition transform hover:scale-105 hover:shadow-lg">
                    Update
                </button>
                <a href="{{ route('users.index') }}" class="bg-gradient-to-r from-gray-600 to-gray-700 text-white px-6 py-3 rounded-lg shadow-md transition transform hover:scale-105 hover:shadow-lg">
                    Kembali
                </a>
            </div>
        </form>
    </section>
</div>
@endsection
