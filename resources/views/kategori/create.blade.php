@extends('layouts.app')

@section('content')
<div class="p-6 bg-gray-900 min-h-screen text-white">
    <div class="max-w-4xl mx-auto">
        <h2 class="text-2xl font-semibold mb-6">Tambah Kategori</h2>

        <form action="{{ route('kategori.store') }}" method="POST" class="bg-gray-800 p-6 rounded-xl shadow" id="addCategoryForm">
            @csrf

            <div class="mb-4">
                <label class="block mb-1">Nama Kategori</label>
                <input type="text" name="nama_kategori" value="{{ old('nama_kategori') }}" class="w-full p-2 rounded bg-gray-700 border border-gray-600 text-white" required>
            </div>

            <div class="mb-4">
                <label class="block mb-1">Deskripsi</label>
                <textarea name="deskripsi" class="w-full p-2 rounded bg-gray-700 border border-gray-600 text-white">{{ old('deskripsi') }}</textarea>
            </div>

            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                Simpan
            </button>
            <a href="{{ route('kategori.index') }}" class="ml-4 text-gray-300 hover:text-white underline">Kembali</a>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // SweetAlert Konfirmasi sebelum Menambahkan Kategori
    document.getElementById('addCategoryForm').addEventListener('submit', function(e) {
        e.preventDefault();

        Swal.fire({
            title: 'Yakin ingin menambahkan kategori?',
            text: "Pastikan data yang diisi sudah benar.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#4CAF50',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, tambah!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();  // Form submit setelah konfirmasi
            }
        });
    });
</script>
@endpush

@endsection
