<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KategoriBarangController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\DetailPengembalianController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('auth.login');
})->name('login');

Route::post('/login', [AuthController::class, 'login'])->name('auth.login');

Route::middleware('auth', 'role:admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/barang', [BarangController::class, 'index'])->name('barang.index');
        Route::get('/barang/create', [BarangController::class, 'create'])->name('barang.create');
        Route::get('/barang/{id}', [BarangController::class, 'show'])->name('barang.show');
        Route::get('/barang/{id}/edit', [BarangController::class, 'edit'])->name('barang.edit');
        Route::post('/barang/store', [BarangController::class, 'store'])->name('barang');
        Route::put('/barang/{id}', [BarangController::class, 'update'])->name('barang.update');
        Route::delete('/barang/{id}', [BarangController::class, 'destroy'])->name('barang.destroy');

        Route::get('/kategori-barang', [KategoriBarangController::class, 'index'])->name('kategori.index');
        Route::get('/kategori-barang/create', [KategoriBarangController::class, 'create'])->name('kategori.create');
        Route::get('/kategori-barang/{id}', [KategoriBarangController::class, 'show'])->name('kategori.show');
        Route::get('/kategori-barang/{id}/edit', [KategoriBarangController::class, 'edit'])->name('kategori.edit');
        Route::post('/kategori-barang/store', [KategoriBarangController::class, 'store'])->name('kategori.store');
        Route::put('/kategori-barang/{id}', [KategoriBarangController::class, 'update'])->name('kategori.update');
        Route::delete('/kategori-barang/{id}', [KategoriBarangController::class, 'destroy'])->name('kategori.destroy');
    
        Route::get('/peminjaman', [PeminjamanController::class, 'index'])->name('peminjaman.index');
        Route::get('/peminjaman/{id}', [PeminjamanController::class, 'show'])->name('peminjaman.show');
        Route::post('/peminjaman/store', [PeminjamanController::class, 'store'])->name('peminjaman.store');
        Route::put('/peminjaman/approve/{id}', [PeminjamanController::class, 'approve'])->name('peminjaman.approve');
        Route::put('/peminjaman/reject/{id}', [PeminjamanController::class, 'reject'])->name('peminjaman.reject');

        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');
        Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::post('/users/store', [UserController::class, 'store'])->name('users');
        Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

        Route::get('/detail-pengembalian', [DetailPengembalianController::class, 'index'])->name('detail-pengembalian.index');
        Route::get('/detail-pengembalian/{id}', [DetailPengembalianController::class, 'show'])->name('detail-pengembalian.show');
        Route::post('/detail-pengembalian/store', [DetailPengembalianController::class, 'store'])->name('detail-pengembalian');
        Route::put('/detail-pengembalian/{id}', [DetailPengembalianController::class, 'update'])->name('detail-pengembalian.update');
        Route::delete('/detail-pengembalian/{id}', [DetailPengembalianController::class, 'destroy'])->name('detail-pengembalian.destroy');
        
        Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/', [LaporanController::class, 'index'])->name('index');
        Route::get('/filter', [LaporanController::class, 'filter'])->name('filter'); 

        Route::get('/laporan/barang', [LaporanController::class, 'barang'])->name('barang');
        Route::get('/laporan/barang/pdf', [LaporanController::class, 'barangPdf'])->name('barang.pdf');
        Route::get('/laporan/barang/excel', [LaporanController::class, 'barangExcel'])->name('barang.excel');

        Route::get('/laporan/peminjaman', [LaporanController::class, 'peminjaman'])->name('peminjaman');
        Route::get('/laporan/peminjaman/pdf', [LaporanController::class, 'peminjamanPdf'])->name('peminjaman.pdf');
        Route::get('/laporan/peminjaman/excel', [LaporanController::class, 'peminjamanExcel'])->name('peminjaman.excel');

        Route::get('/laporan/pengembalian', [LaporanController::class, 'pengembalian'])->name('pengembalian');
        Route::get('/laporan/pengembalian/pdf', [LaporanController::class, 'pengembalianPdf'])->name('pengembalian.pdf');
        Route::get('/laporan/pengembalian/excel', [LaporanController::class, 'pengembalianExcel'])->name('pengembalian.excel');
});
});

// Logout (web)
Route::get('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');
