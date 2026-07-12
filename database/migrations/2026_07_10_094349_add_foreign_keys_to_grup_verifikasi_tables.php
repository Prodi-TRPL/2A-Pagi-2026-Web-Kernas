<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Hapus data yatim (orphaned records) sebelum menambahkan Foreign Key
        // Hapus data grup_verifikasi_pengajuan yang id_pengajuan-nya tidak ada di tabel pengajuan
        DB::table('grup_verifikasi_pengajuan')
            ->whereNotIn('id_pengajuan', function ($query) {
                $query->select('id')->from('pengajuan');
            })
            ->delete();

        // Hapus data grup_verifikasi_pengajuan yang id_pengguna-nya tidak ada di tabel pengguna
        DB::table('grup_verifikasi_pengajuan')
            ->whereNotIn('id_pengguna', function ($query) {
                $query->select('id')->from('pengguna');
            })
            ->delete();

        // Hapus data grup_verifikasi_dokumen yang id_pengguna-nya tidak ada di tabel pengguna
        DB::table('grup_verifikasi_dokumen')
            ->whereNotIn('id_pengguna', function ($query) {
                $query->select('id')->from('pengguna');
            })
            ->delete();

        // 2. Tambahkan Foreign Key
        Schema::table('grup_verifikasi_pengajuan', function (Blueprint $table) {
            $table->foreign('id_pengajuan')->references('id')->on('pengajuan')->onDelete('cascade');
            $table->foreign('id_pengguna')->references('id')->on('pengguna')->onDelete('cascade');
        });

        Schema::table('grup_verifikasi_dokumen', function (Blueprint $table) {
            $table->foreign('id_pengguna')->references('id')->on('pengguna')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grup_verifikasi_pengajuan', function (Blueprint $table) {
            $table->dropForeign(['id_pengajuan']);
            $table->dropForeign(['id_pengguna']);
        });

        Schema::table('grup_verifikasi_dokumen', function (Blueprint $table) {
            $table->dropForeign(['id_pengguna']);
        });
    }
};
