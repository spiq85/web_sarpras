<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangController; 
use App\Http\Controllers\KategoriBarangController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\DetailPeminjamanController;
use App\Http\Controllers\DetailPengembalianController;
use App\Http\Controllers\DetailPengembalianApiController;
use App\Http\Controllers\UserController;

// ------------------- AUTH -------------------
Route::post('/login', [AuthController::class, 'apiLogin']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'apiLogout']);

    // ------------------- USER ROLE -------------------
    Route::middleware(['role:user'])->group(function () {
        Route::get('/user', [UserController::class, 'index']);

        // ---------------- KATEGORI BARANG ----------------
        Route::get('/barang', [BarangController::class, 'apiIndex']);
        Route::get('/barang/{id}', [BarangController::class, 'show']);

        // ---------------- MENGAJUKAN PEMINJAMAN ----------------
        Route::post('/detail-peminjaman', [DetailPeminjamanController::class, 'store']);
        Route::get('/detail-peminjaman/{id}', [DetailPeminjamanController::class, 'show'])->name('detail-peminjaman.show');
        Route::post('/peminjaman', [PeminjamanController::class, 'store']);
        Route::get('/peminjaman/{id}', [PeminjamanController::class, 'show']);
        Route::get('/peminjaman-user', [PeminjamanController::class, 'userPeminjaman']);

        // ---------------- DETAIL PEMINJAMAN ----------------
    Route::get('/detail-pengembalian', [DetailPengembalianApiController::class, 'index'])->name('detail-pengembalian.index');
    Route::post('/detail-pengembalian', [DetailPengembalianApiController::class, 'store'])->name('detail-pengembalian.store');
    Route::get('/detail-pengembalian/{id}', [DetailPengembalianApiController::class, 'show'])->name('detail-pengembalian.show');
    Route::put('/detail-pengembalian/{id}', [DetailPengembalianApiController::class, 'update'])->name('detail-pengembalian.update');
    Route::get('/detail-pengembalian-user', [DetailPengembalianApiController::class, 'userPengembalian'])->name('api.detail-pengembalian.user');


    });
});
// ------------------- ADMIN ROLE -------------------
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    // ---------------- USERS ----------------
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

    // ---------------- BARANG ----------------
    Route::post('/barang', [BarangController::class, 'store'])->name('barang.store');
    Route::put('/barang/{id}', [BarangController::class, 'update'])->name('barang.update');
    Route::delete('/barang/{id}', [BarangController::class, 'destroy'])->name('barang.destroy');

    // ---------------- KATEGORI BARANG ----------------
    Route::get('/kategori-barang', [KategoriBarangController::class, 'index'])->name('kategori-barang.index');
    Route::post('/kategori-barang', [KategoriBarangController::class, 'store'])->name('kategori-barang.store');
    Route::get('/kategori-barang/{id}', [KategoriBarangController::class, 'show'])->name('kategori-barang.show');
    Route::put('/kategori-barang/{id}', [KategoriBarangController::class, 'update'])->name('kategori-barang.update');
    Route::delete('/kategori-barang/{id}', [KategoriBarangController::class, 'destroy'])->name('kategori-barang.destroy');

    // ---------------- PEMINJAMAN ----------------
    Route::get('/peminjaman', [PeminjamanController::class, 'index'])->name('peminjaman.index');
    Route::get('/peminjaman/{id}', [PeminjamanController::class, 'show'])->name('peminjaman.show');
    Route::post('/peminjaman/store', [PeminjamanController::class, 'store'])->name('peminjaman.store');
    Route::put('/peminjaman/approve/{id}', [PeminjamanController::class, 'approve'])->name('peminjaman.approve');
    Route::put('/peminjaman/reject/{id}', [PeminjamanController::class, 'reject'])->name('peminjaman.reject');
    // ---------------- DETAIL PEMINJAMAN ----------------
    Route::get('/detail-peminjaman', [DetailPeminjamanController::class, 'index'])->name('detail-peminjaman.index');
    Route::put('/detail-peminjaman/{id}', [DetailPeminjamanController::class, 'update'])->name('detail-peminjaman.update');
    Route::delete('/detail-peminjaman/{id}', [DetailPeminjamanController::class, 'destroy'])->name('detail-peminjaman.destroy');

    // ---------------- DETAIL PENGEMBALIAN ----------------
    Route::delete('/detail-pengembalian/{id}', [DetailPengembalianController::class, 'destroy'])->name('detail-pengembalian.destroy');
    Route::put('/detail-pengembalian/{id}/approve', [DetailPengembalianController::class, 'approve'])->name('detail-pengembalian.approve');
    Route::put('/detail-pengembalian/{id}/reject', [DetailPengembalianController::class, 'reject'])->name('detail-pengembalian.reject');
});
