<?php

use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\RekapController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GuruBk\PengajuanController as GuruPengajuanController;
use App\Http\Controllers\PesanKonsultasiController;
use App\Http\Controllers\Siswa\PengajuanController as SiswaPengajuanController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profil', [AuthController::class, 'profile'])->name('profil');

    // Siswa
    Route::middleware('role:siswa')->prefix('siswa')->name('siswa.')->group(function () {
        Route::get('/pengajuan', [SiswaPengajuanController::class, 'index'])->name('pengajuan.index');
        Route::get('/pengajuan/buat', [SiswaPengajuanController::class, 'create'])->name('pengajuan.create');
        Route::post('/pengajuan', [SiswaPengajuanController::class, 'store'])->name('pengajuan.store');
        Route::get('/pengajuan/{pengajuan}', [SiswaPengajuanController::class, 'show'])->name('pengajuan.show');
        Route::post('/pengajuan/{pengajuan}/batalkan', [SiswaPengajuanController::class, 'cancel'])->name('pengajuan.cancel');
        Route::post('/pengajuan/{pengajuan}/pesan', [PesanKonsultasiController::class, 'store'])->name('pengajuan.pesan.store');
    });

    // Guru BK
    Route::middleware('role:guru_bk')->prefix('guru-bk')->name('guru.')->group(function () {
        Route::get('/pengajuan', [GuruPengajuanController::class, 'index'])->name('pengajuan.index');
        Route::get('/pengajuan/{pengajuan}', [GuruPengajuanController::class, 'show'])->name('pengajuan.show');
        Route::post('/pengajuan/{pengajuan}/aksi', [GuruPengajuanController::class, 'action'])->name('pengajuan.action');
        Route::post('/pengajuan/{pengajuan}/pesan', [PesanKonsultasiController::class, 'store'])->name('pengajuan.pesan.store');
        Route::get('/jadwal', [GuruPengajuanController::class, 'jadwalIndex'])->name('jadwal.index');
    });

    // Admin
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/buat', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::post('/users/{user}/toggle', [UserController::class, 'toggle'])->name('users.toggle');

        Route::get('/kelas', [KelasController::class, 'index'])->name('kelas.index');
        Route::post('/kelas', [KelasController::class, 'store'])->name('kelas.store');
        Route::get('/kelas/{kelas}/edit', [KelasController::class, 'edit'])->name('kelas.edit');
        Route::put('/kelas/{kelas}', [KelasController::class, 'update'])->name('kelas.update');
        Route::post('/kelas/{kelas}/toggle', [KelasController::class, 'toggle'])->name('kelas.toggle');

        Route::get('/kategori', [KategoriController::class, 'index'])->name('kategori.index');
        Route::post('/kategori', [KategoriController::class, 'store'])->name('kategori.store');
        Route::get('/kategori/{kategori}/edit', [KategoriController::class, 'edit'])->name('kategori.edit');
        Route::put('/kategori/{kategori}', [KategoriController::class, 'update'])->name('kategori.update');
        Route::post('/kategori/{kategori}/toggle', [KategoriController::class, 'toggle'])->name('kategori.toggle');

        Route::get('/rekap', [RekapController::class, 'index'])->name('rekap.index');
    });
});
