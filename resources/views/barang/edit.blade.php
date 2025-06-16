@extends('layouts.app')

@section('content')
<div class="p-6 bg-gray-900 min-h-screen text-white">
    <div class="max-w-4xl mx-auto">
        <h2 class="text-2xl font-semibold mb-6">Edit Barang</h2>

        <form action="{{ route('barang.update', $barang->id_barang) }}" method="POST" enctype="multipart/form-data" class="bg-gray-800 p-6 rounded-xl shadow">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block mb-1">Nama Barang</label>
                <input type="text" name="nama_barang" value="{{ old('nama_barang', $barang->nama_barang) }}" class="w-full p-2 rounded bg-gray-700 border border-gray-600 text-white @error('nama_barang') border-red-500 @enderror" required>
                @error('nama_barang')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block mb-1">Kode Barang</label>
                {{-- Make it readonly if you want to strictly control auto-generated codes --}}
                <input type="text" name="kode_barang" value="{{ old('kode_barang', $barang->kode_barang) }}" class="w-full p-2 rounded bg-gray-700 border border-gray-600 text-gray-400 cursor-not-allowed" readonly>
                <p class="text-gray-400 text-xs mt-1">Kode barang digenerate otomatis berdasarkan kategori. Tidak bisa diubah manual.</p>
                @error('kode_barang')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block mb-1">Kategori</label>
                <select name="id_category" class="w-full p-2 rounded bg-gray-700 border border-gray-600 text-white @error('id_category') border-red-500 @enderror" required>
                    @foreach($kategori as $kat)
                        <option value="{{ $kat->id_category }}" {{ old('id_category', $barang->id_category) == $kat->id_category ? 'selected' : '' }}>
                            {{ $kat->nama_kategori }}
                        </option>
                    @endforeach
                </select>
                @error('id_category')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
                <p class="text-gray-400 text-xs mt-1">Jika kategori diubah, kode barang akan digenerate ulang.</p>
            </div>

            <div class="mb-4">
                <label class="block mb-1">Stock</label>
                <input type="number" name="stock" value="{{ old('stock', $barang->stock) }}" class="w-full p-2 rounded bg-gray-700 border border-gray-600 text-white @error('stock') border-red-500 @enderror" required min="1">
                @error('stock')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block mb-1">Brand</label>
                <input type="text" name="brand" value="{{ old('brand', $barang->brand) }}" class="w-full p-2 rounded bg-gray-700 border border-gray-600 text-white @error('brand') border-red-500 @enderror">
                @error('brand')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block mb-1">Status</label>
                <select name="status" class="w-full p-2 rounded bg-gray-700 border border-gray-600 text-white @error('status') border-red-500 @enderror">
                    <option value="">-- Pilih Status --</option>
                    <option value="tersedia" {{ old('status', $barang->status) == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                    <option value="dipinjam" {{ old('status', $barang->status) == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                </select>
                @error('status')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block mb-1">Kondisi</label>
                <select name="kondisi_barang" class="w-full p-2 rounded bg-gray-700 border border-gray-600 text-white @error('kondisi_barang') border-red-500 @enderror">
                    <option value="">-- Pilih Kondisi --</option>
                    <option value="baik" {{ old('kondisi_barang', $barang->kondisi_barang) == 'baik' ? 'selected' : '' }}>Baik</option>
                    <option value="rusak" {{ old('kondisi_barang', $barang->kondisi_barang) == 'rusak' ? 'selected' : '' }}>Rusak</option>
                    <option value="dll" {{ old('kondisi_barang', $barang->kondisi_barang) == 'dll' ? 'selected' : '' }}>Dll</option>
                </select>
                @error('kondisi_barang')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block mb-1">Gambar Barang (opsional)</label>
                <input type="file" name="gambar_barang" id="gambar_barang_edit" class="w-full p-2 rounded bg-gray-700 border border-gray-600 text-white file:bg-blue-600 file:text-white file:rounded file:px-3 file:py-1 file:mr-2 @error('gambar_barang') border-red-500 @enderror">
                @error('gambar_barang')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror

                <div class="mt-3">
                    <img id="preview_gambar_edit" src="{{ $barang->gambar_barang ? asset('storage/gambar_barang/' . $barang->gambar_barang) : '#' }}" alt="Preview Gambar" class="{{ $barang->gambar_barang ? '' : 'hidden' }} max-h-64 rounded border border-gray-500">
                </div>
            </div>

            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Update</button>
            <a href="{{ route('barang.index') }}" class="ml-4 text-gray-300 hover:text-white underline">Kembali</a>
        </form>
    </div>
</div>

{{-- Script Preview Gambar for Edit --}}
<script>
    document.getElementById('gambar_barang_edit').addEventListener('change', function(e) {
        const [file] = e.target.files;
        const preview = document.getElementById('preview_gambar_edit');

        if (file) {
            preview.src = URL.createObjectURL(file);
            preview.classList.remove('hidden');
        } else {
            // If no file selected, and there was an old image, show the old image
            // Otherwise, hide the preview
            if ("{{ $barang->gambar_barang }}") {
                preview.src = "{{ asset('storage/gambar_barang/' . $barang->gambar_barang) }}";
                preview.classList.remove('hidden');
            } else {
                preview.src = '#';
                preview.classList.add('hidden');
            }
        }
    });

    // Display validation errors with SweetAlert if any
    @if ($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Gagal Memperbarui Barang!',
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

    // Display custom error from controller
    @if (session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: '{{ session('error') }}',
            confirmButtonText: 'Oke',
            confirmButtonColor: '#d33',
            background: '#1e293b',
            color: '#f3f4f6',
        });
    @endif
</script>
@endsection