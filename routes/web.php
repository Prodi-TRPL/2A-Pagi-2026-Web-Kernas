<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PegawaiController;

Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/', [AuthController::class, 'login'])->name('login.post')->middleware('throttle:5,1');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DokumenController;

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dokumen-terbit', [DashboardController::class, 'dokumenTerbit'])->name('dokumen-terbit');
Route::post('/dokumen-terbit/langsung', [DokumenController::class, 'uploadLangsung'])->name('dokumen.upload-langsung');
Route::delete('/dokumen-terbit/{id}', [DokumenController::class, 'hapusPermanen']);

Route::get('/setup/manajemen-karyawan', [PegawaiController::class, 'index']);
Route::post('/setup/manajemen-karyawan', [PegawaiController::class, 'store']);
Route::put('/setup/manajemen-karyawan/{id}/profil', [PegawaiController::class, 'updateProfil']);
Route::put('/setup/manajemen-karyawan/{id}/peran', [PegawaiController::class, 'updatePeran']);
Route::delete('/setup/manajemen-karyawan/{id}', [PegawaiController::class, 'destroy']);

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
Route::get('/pengajuan/form/contoh-surat-satu', [PengajuanController::class, 'formContohSuratSatu']);
Route::post('/pengajuan/form/contoh-surat-satu', [PengajuanController::class, 'storeContohSuratSatu']);
Route::get('/pengajuan/form/dinas-arahan', [PengajuanController::class, 'formDinasArahan']);
Route::post('/pengajuan/form/dinas-arahan', [PengajuanController::class, 'storeDinasArahan']);
Route::get('/pengajuan/form/dinas-instruksi', [PengajuanController::class, 'formDinasInstruksi']);
Route::post('/pengajuan/form/dinas-instruksi', [PengajuanController::class, 'storeDinasInstruksi']);
Route::get('/pengajuan/form/arahan-edaran', [PengajuanController::class, 'formArahanEdaran']);
Route::post('/pengajuan/form/arahan-edaran', [PengajuanController::class, 'storeArahanEdaran']);
Route::get('/pengajuan/form/arahan-perintah', [PengajuanController::class, 'formArahanPerintah']);
Route::post('/pengajuan/form/arahan-perintah', [PengajuanController::class, 'storeArahanPerintah']);
Route::get('/pengajuan/form/arahan-keputusan', [PengajuanController::class, 'formArahanKeputusan']);
Route::post('/pengajuan/form/arahan-keputusan', [PengajuanController::class, 'storeArahanKeputusan']);
Route::get('/pengajuan/form/arahan-tugas-biasa', [PengajuanController::class, 'formArahanTugasBiasa']);
Route::post('/pengajuan/form/arahan-tugas-biasa', [PengajuanController::class, 'storeArahanTugasBiasa']);
Route::get('/pengajuan/form/arahan-tugas-tabel', [PengajuanController::class, 'formArahanTugasTabel']);
Route::post('/pengajuan/form/arahan-tugas-tabel', [PengajuanController::class, 'storeArahanTugasTabel']);
Route::get('/pengajuan/form/korespondensi-nota', [PengajuanController::class, 'formKorespondensiNota']);
Route::post('/pengajuan/form/korespondensi-nota', [PengajuanController::class, 'storeKorespondensiNota']);
Route::get('/pengajuan/form/korespondensi-dinas', [PengajuanController::class, 'formKorespondensiDinas']);
Route::post('/pengajuan/form/korespondensi-dinas', [PengajuanController::class, 'storeKorespondensiDinas']);
Route::get('/pengajuan/form/korespondensi-undangan', [PengajuanController::class, 'formKorespondensiUndangan']);
Route::post('/pengajuan/form/korespondensi-undangan', [PengajuanController::class, 'storeKorespondensiUndangan']);

Route::get('/pengajuan/form/dinas-khusus-keterangan', [PengajuanController::class, 'formDinasKhususKeterangan']);
Route::post('/pengajuan/form/dinas-khusus-keterangan', [PengajuanController::class, 'storeDinasKhususKeterangan']);

Route::get('/pengajuan/form/dinas-khusus-pengantar', [PengajuanController::class, 'formDinasKhususPengantar']);
Route::post('/pengajuan/form/dinas-khusus-pengantar', [PengajuanController::class, 'storeDinasKhususPengantar']);

Route::get('/pengajuan/form/dinas-khusus-pengumuman', [PengajuanController::class, 'formDinasKhususPengumuman']);
Route::post('/pengajuan/form/dinas-khusus-pengumuman', [PengajuanController::class, 'storeDinasKhususPengumuman']);

Route::get('/pengajuan/form/dinas-khusus-pernyataan', [PengajuanController::class, 'formDinasKhususPernyataan']);
Route::post('/pengajuan/form/dinas-khusus-pernyataan', [PengajuanController::class, 'storeDinasKhususPernyataan']);

Route::get('/pengajuan/form/dinas-khusus-rekomendasi', [PengajuanController::class, 'formDinasKhususRekomendasi']);
Route::post('/pengajuan/form/dinas-khusus-rekomendasi', [PengajuanController::class, 'storeDinasKhususRekomendasi']);
Route::get('/pengajuan/{id}/edit', [PengajuanController::class, 'editDoc']);
Route::get('/pengajuan/{id}/lampiran', [PengajuanController::class, 'viewLampiran']);
Route::get('/pengajuan/{pengajuanId}/lampiran/{lampiranId}/unduh', [PengajuanController::class, 'unduhLampiran'])->name('lampiran.unduh');
Route::post('/pengajuan/{id}/kirim', [PengajuanController::class, 'kirimVerifikasi']);

Route::get('/template/{id}/edit', [TemplateSuratController::class, 'editTemplate'])->name('template.edit');
Route::put('/setup/template-surat/{id}', [TemplateSuratController::class, 'update']);
Route::post('/template/{id}/simpan-dokumen', [TemplateSuratController::class, 'simpanDokumen']);

// Dokumen Routes
Route::get('/dokumen/{id}/unduh', [DokumenController::class, 'unduh'])->name('dokumen.unduh');
Route::get('/dokumen/{id}/lihat', [DokumenController::class, 'lihat'])->name('dokumen.lihat');
Route::post('/pengajuan/{id}/admin-kirim', [PengajuanController::class, 'adminKirimVerifikator']);
Route::post('/pengajuan/{id}/admin-kirim-ulang', [PengajuanController::class, 'adminKirimUlangVerifikator']);
Route::post('/pengajuan/{id}/admin-kembalikan', [PengajuanController::class, 'adminKembalikanPengusul']);
Route::post('/pengajuan/{id}/hapus', [PengajuanController::class, 'hapusPengajuan']);
Route::post('/pengajuan/{id}/terima', [PengajuanController::class, 'terimaPengajuan']);
Route::post('/pengajuan/{id}/tolak', [PengajuanController::class, 'tolakPengajuan']);

use App\Http\Controllers\PeraturanController;
Route::get('/setup/peraturan', [PeraturanController::class, 'index']);
Route::post('/setup/peraturan', [PeraturanController::class, 'store']);
Route::put('/setup/peraturan/{id}', [PeraturanController::class, 'update']);
Route::delete('/setup/peraturan/{id}', [PeraturanController::class, 'destroy']);

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
