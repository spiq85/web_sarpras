@extends('layouts.app')

@section('content')
<div class="p-6 bg-gray-900 min-h-screen text-white">
    <div class="max-w-4xl mx-auto">
        <h2 class="text-2xl font-semibold mb-6">Tambah Barang</h2>

        <form action="{{ route('barang.store') }}" method="POST" enctype="multipart/form-data" class="bg-gray-800 p-6 rounded-xl shadow" id="addBarangForm">
            @csrf

            <div class="mb-4">
                <label class="block mb-1">Kode Barang</label>
                <input type="text" name="kode_barang" value="{{ old('kode_barang') }}" class="w-full p-2 rounded bg-gray-700 border border-gray-600 text-white" required>
            </div>

            <div class="mb-4">
                <label class="block mb-1">Nama Barang</label>
                <input type="text" name="nama_barang" value="{{ old('nama_barang') }}" class="w-full p-2 rounded bg-gray-700 border border-gray-600 text-white" required>
            </div>

            <div class="mb-4">
                <label class="block mb-1">Kategori</label>
                <select name="id_category" class="w-full p-2 rounded bg-gray-700 border border-gray-600 text-white" required>
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($kategori as $kat)
                        <option value="{{ $kat->id_category }}" {{ old('id_category') == $kat->id_category ? 'selected' : '' }}>
                            {{ $kat->nama_kategori }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block mb-1">Stock</label>
                <input type="number" name="stock" value="{{ old('stock') }}" class="w-full p-2 rounded bg-gray-700 border border-gray-600 text-white" required>
            </div>

            <div class="mb-4">
                <label class="block mb-1">Kondisi</label>
                <select name="kondisi_barang" class="w-full p-2 rounded bg-gray-700 border border-gray-600 text-white">
                    <option value="">-- Pilih Kondisi --</option>
                    <option value="baik" {{ old('kondisi_barang') == 'baik' ? 'selected' : '' }}>Baik</option>
                    <option value="rusak" {{ old('kondisi_barang') == 'rusak' ? 'selected' : '' }}>Rusak</option>
                    <option value="dll" {{ old('kondisi_barang') == 'dll' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block mb-1">Brand / Merek</label>
                <input type="text" name="brand" value="{{ old('brand') }}" class="w-full p-2 rounded bg-gray-700 border border-gray-600 text-white">
            </div>

            <div class="mb-4">
                <label class="block mb-1">Status</label>
                <select name="status" class="w-full p-2 rounded bg-gray-700 border border-gray-600 text-white">
                    <option value="">-- Pilih Status --</option>
                    <option value="tersedia" {{ old('status') == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                    <option value="dipinjam" {{ old('status') == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                </select>
            </div>

            <div class="mb-6">
                <label class="block mb-2">Gambar Barang (opsional)</label>
                <input type="file" name="gambar_barang" id="gambar_barang" class="w-full p-2 rounded bg-gray-700 border border-gray-600 text-white file:bg-blue-600 file:text-white file:rounded file:px-3 file:py-1 file:mr-2">
                
                <div class="mt-3">
                    <img id="preview_gambar" src="#" alt="Preview Gambar" class="hidden max-h-64 rounded border border-gray-500">
                </div>
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Simpan</button>
                <a href="{{ route('barang.index') }}" class="ml-4 text-gray-300 hover:text-white underline">Kembali</a>
            </div>
        </form>
    </div>
</div>

{{-- Script Preview Gambar --}}
<script>
    document.getElementById('gambar_barang').addEventListener('change', function(e) {
        const [file] = e.target.files;
        const preview = document.getElementById('preview_gambar');

        if (file) {
            preview.src = URL.createObjectURL(file);
            preview.classList.remove('hidden');
        } else {
            preview.src = '#';
            preview.classList.add('hidden');
        }
    });

    // SweetAlert Konfirmasi sebelum Menambah Barang
    document.getElementById('addBarangForm').addEventListener('submit', function(e) {
        e.preventDefault();

        Swal.fire({
            title: 'Yakin ingin menambahkan barang?',
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@endsection
