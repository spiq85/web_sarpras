@extends('layouts.app')

@section('content')
<div class="p-6 bg-gray-900 min-h-screen text-white">
    <div class="max-w-4xl mx-auto">
        <h2 class="text-2xl font-semibold mb-6">Edit Kategori</h2>

        <form action="{{ route('kategori.update', $kategori->id_category) }}" method="POST" class="bg-gray-800 p-6 rounded-xl shadow">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block mb-1">Nama Kategori</label>
                <input type="text" name="nama_kategori" value="{{ old('nama_kategori', $kategori->nama_kategori) }}" class="w-full p-2 rounded bg-gray-700 border border-gray-600 text-white @error('nama_kategori') border-red-500 @enderror" required>
                @error('nama_kategori')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block mb-1">Prefix Kode</label>
                <input type="text" name="prefix_kode" value="{{ old('prefix_kode', $kategori->prefix_kode) }}" class="w-full p-2 rounded bg-gray-700 border border-gray-600 text-white @error('prefix_kode') border-red-500 @enderror" placeholder="Contoh: ELK, BUK, ALK" required maxlength="10">
                @error('prefix_kode')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
                <p class="text-gray-400 text-xs mt-1">Digunakan untuk awalan kode barang (misal: ELK001).</p>
            </div>

            <div class="mb-4">
                <label class="block mb-1">Deskripsi</label>
                <textarea name="deskripsi" class="w-full p-2 rounded bg-gray-700 border border-gray-600 text-white @error('deskripsi') border-red-500 @enderror">{{ old('deskripsi', $kategori->deskripsi) }}</textarea>
                @error('deskripsi')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Update</button>
            <a href="{{ route('kategori.index') }}" class="ml-4 text-gray-300 hover:text-white underline">Kembali</a>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Display validation errors with SweetAlert if any
    @if ($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Gagal Memperbarui Kategori!',
            html: '<ul class="text-left">' +
                @foreach ($errors->all() as $error)
                    '<li>- {{ $error }}</li>' +
                @endforeach
                '</ul>',
            confirmButtonText: 'Oke',
            confirmButtonColor: '#d33',
            background: '#1e293b',
            color: '#f3f4f6',
        });
    @endif
</script>
@endpush
@endsection