@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-900 to-black text-white p-6">

    {{-- Header --}}
    <div class="flex justify-between items-center mb-8">
        <h2 class="text-3xl font-semibold">👥 Tambah Pengguna</h2>
        <a href="{{ route('users.index') }}"
            class="bg-gradient-to-r from-gray-500 to-gray-700 hover:from-gray-600 hover:to-gray-800 text-white px-5 py-2 rounded-lg shadow-md transition transform hover:scale-105 hover:shadow-lg">
            Kembali ke Daftar Pengguna
        </a>
    </div>

    {{-- Form Tambah Pengguna --}}
    <section class="bg-gray-800 p-6 rounded-xl shadow-lg">
        <h3 class="text-xl font-medium mb-4 border-b border-gray-700 pb-2">Tambah Pengguna Baru</h3>
        <form action="{{ route('users.store') }}" method="POST" id="addUserForm">
            @csrf
            <div class="mb-3">
                <label class="block">Username</label>
                <input type="text" name="username" class="form-control w-full p-3 rounded-md bg-gray-700 text-white border border-gray-600 focus:ring-teal-500 focus:ring-2 focus:outline-none" required>
            </div>

            <div class="mb-3">
                <label class="block">Name</label>
                <input type="text" name="name" class="form-control w-full p-3 rounded-md bg-gray-700 text-white border border-gray-600 focus:ring-teal-500 focus:ring-2 focus:outline-none" required>
            </div>

            <div class="mb-3">
                <label class="block">Email</label>
                <input type="email" name="email" class="form-control w-full p-3 rounded-md bg-gray-700 text-white border border-gray-600 focus:ring-teal-500 focus:ring-2 focus:outline-none" required>
            </div>

            <div class="mb-3">
                <label class="block">Password</label>
                <input type="password" name="password" class="form-control w-full p-3 rounded-md bg-gray-700 text-white border border-gray-600 focus:ring-teal-500 focus:ring-2 focus:outline-none" required>
            </div>

            <div class="mb-3">
                <label class="block">Role</label>
                <select name="role" class="form-control w-full p-3 rounded-md bg-gray-700 text-white border border-gray-600 focus:ring-teal-500 focus:ring-2 focus:outline-none" required>
                    <option value="admin">Admin</option>
                    <option value="user">User</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="block">Kelas</label>
                <select name="class" class="form-control w-full p-3 rounded-md bg-gray-700 text-white border border-gray-600 focus:ring-teal-500 focus:ring-2 focus:outline-none" required>
                    <option value="X">X</option>
                    <option value="XI">XI</option>
                    <option value="XII">XII</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="block">Jurusan</label>
                <select name="major" class="form-control w-full p-3 rounded-md bg-gray-700 text-white border border-gray-600 focus:ring-teal-500 focus:ring-2 focus:outline-none" required>
                    <option value="RPL">RPL</option>
                    <option value="TJKT">TJKT</option>
                    <option value="PSPT">PSPT</option>
                    <option value="ANIMASI">ANIMASI</option>
                    <option value="TE">TE</option>
                </select>
            </div>

            <button type="submit" class="bg-gradient-to-r from-teal-500 to-teal-700 hover:from-teal-600 hover:to-teal-800 text-white px-5 py-2 rounded-lg shadow-md transition transform hover:scale-105 hover:shadow-lg">
                Simpan
            </button>
        </form>
    </section>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // SweetAlert Konfirmasi sebelum Menambahkan Pengguna
    document.getElementById('addUserForm').addEventListener('submit', function(e) {
        e.preventDefault();

        Swal.fire({
            title: 'Yakin ingin menambahkan pengguna?',
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
