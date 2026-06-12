<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PegawaiController;

Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/', [AuthController::class, 'login'])->name('login.post');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

use App\Http\Controllers\DashboardController;

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/setup/manajemen-karyawan', [PegawaiController::class, 'index']);
Route::post('/setup/manajemen-karyawan', [PegawaiController::class, 'store']);
Route::put('/setup/manajemen-karyawan/{id}/profil', [PegawaiController::class, 'updateProfil']);
Route::put('/setup/manajemen-karyawan/{id}/peran', [PegawaiController::class, 'updatePeran']);

use App\Http\Controllers\GrupVerifikasiController;

Route::get('/setup/grup-verifikasi', [GrupVerifikasiController::class, 'index']);
Route::post('/setup/grup-verifikasi', [GrupVerifikasiController::class, 'store']);
Route::put('/setup/grup-verifikasi/{id}', [GrupVerifikasiController::class, 'update']);
Route::delete('/setup/grup-verifikasi/{id}', [GrupVerifikasiController::class, 'destroy']);
Route::post('/setup/grup-verifikasi/{id}/anggota', [GrupVerifikasiController::class, 'addAnggota']);
Route::delete('/setup/grup-verifikasi/{id}/anggota/{id_pengguna}', [GrupVerifikasiController::class, 'removeAnggota']);

use App\Http\Controllers\TemplateSuratController;
use App\Http\Controllers\OnlyOfficeController;

Route::get('/setup/template-surat', [TemplateSuratController::class, 'index']);
Route::post('/onlyoffice/callback/{id}', [OnlyOfficeController::class, 'callback']);
Route::post('/onlyoffice/callback/pengajuan/{id}', [OnlyOfficeController::class, 'callbackPengajuan']);

use App\Http\Controllers\PengajuanController;
Route::get('/pengajuan-surat', [PengajuanController::class, 'index']);
Route::get('/pengajuan/baru', [PengajuanController::class, 'create']);
Route::post('/pengajuan/baru', [PengajuanController::class, 'store']);
Route::get('/pengajuan/{id}/edit', [PengajuanController::class, 'editDoc']);

Route::get('/setup/peraturan', function () {
    if (!session()->has('pengguna')) return redirect('/');
    return view('peraturan');
});

Route::get('/dashboard-admin', function () {
    return view('Admin/dashboard-admin');
});

Route::get('/penggawai', function () {
    return view('penggawai');
});

Route::get('/login', function () {
    return view('login');
});

Route::get('/riwayat-admin', function (){
    return view('Admin/riwayat-admin');
});
