<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dokumen;
use Illuminate\Support\Facades\Storage;

class DokumenController extends Controller
{
    // ini untuk mengecek apakah pengguna memiliki hak akses (pembuat, anggota, atau verifikator) untuk melihat dokumen
    private function checkAccess($dokumen)
    {
        $user = session('pengguna');
        if (!$user) return false;
        if ($user['is_admin']) return true;
        
        $id = $user['id'];
        
        if ($dokumen->id_pengguna == $id) return true;
        
        $isAnggota = \Illuminate\Support\Facades\DB::table('anggota_dokumen')
            ->where('id_dokumen', $dokumen->id)
            ->where('id_pengguna', $id)
            ->exists();
            
        if ($isAnggota) return true;
        
        $isVerifikator = \Illuminate\Support\Facades\DB::table('grup_verifikasi_dokumen')
            ->where('id_dokumen', $dokumen->id)
            ->where('id_pengguna', $id)
            ->exists();
            
        return $isVerifikator;
    }

    // ini untuk mengunduh file fisik dari dokumen yang diminta jika pengguna memiliki akses
    public function unduh($id)
    {
        if (!session()->has('pengguna')) return redirect('/');
        
        $dokumen = Dokumen::findOrFail($id);
        
        if (!$this->checkAccess($dokumen)) {
            return back()->with('error', 'Anda tidak memiliki akses ke dokumen ini.');
        }

        $path = $dokumen->filepath;
        if (!Storage::disk('public')->exists($path)) {
            return back()->with('error', 'File tidak ditemukan di server.');
        }

        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $filename = ($dokumen->nama_dokumen ?? 'Dokumen') . '.' . $extension;
        
        return Storage::disk('public')->download($path, $filename);
    }

    // ini untuk menampilkan (preview) file dokumen secara langsung di browser
    public function lihat($id)
    {
        if (!session()->has('pengguna')) return redirect('/');
        
        $dokumen = Dokumen::findOrFail($id);
        
        if (!$this->checkAccess($dokumen)) {
            return back()->with('error', 'Anda tidak memiliki akses ke dokumen ini.');
        }

        $path = $dokumen->filepath;
        if (!Storage::disk('public')->exists($path)) {
            return back()->with('error', 'File tidak ditemukan di server.');
        }

        return Storage::disk('public')->response($path);
    }

    // ini untuk menangani proses unggah dokumen fisik secara langsung oleh admin tanpa melalui proses pengajuan
    public function uploadLangsung(Request $request)
    {
        if (!session()->has('pengguna')) return redirect('/');
        $pengguna = session('pengguna');
        
        if (!$pengguna['is_admin']) {
            return redirect('/dokumen-terbit')->with('error', 'Hanya Admin yang dapat mengunggah dokumen langsung.');
        }

        $request->validate([
            'file_pdf' => 'required|mimes:pdf|max:10240', // max 10MB
            'nama_dokumen' => 'required|string|max:200',
            'nomor_surat' => 'required|string|max:100|unique:nomor_dokumen,nomor_terformat',
            'tipe_surat' => 'required|string|in:Naskah Dinas Arahan,Naskah Dinas Korespondensi,Naskah Dinas Khusus',
        ]);

        try {
            $file = $request->file('file_pdf');
            $fileName = 'langsung_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            
            // Simpan ke direktori khusus untuk dokumen langsung atau pengajuan
            $filePath = $file->storeAs('dokumen_langsung', $fileName, 'public');

            $tahun = date('Y');
            
            // Generate urutan untuk nomor_dokumen
            $lastNomor = \App\Models\NomorDokumen::where('tipe', $request->tipe_surat)
                            ->where('tahun', $tahun)
                            ->orderBy('urutan', 'desc')
                            ->first();
            $urutan = $lastNomor ? $lastNomor->urutan + 1 : 1;

            $nomorDokumen = \App\Models\NomorDokumen::create([
                'tipe' => $request->tipe_surat,
                'tahun' => $tahun,
                'urutan' => $urutan,
                'nomor_terformat' => $request->nomor_surat,
                'created_at' => now(),
            ]);

            $dokumenBaru = Dokumen::create([
                'id_nomor_dokumen' => $nomorDokumen->id,
                'id_pengguna' => $pengguna['id'],
                'tipe' => $request->tipe_surat,
                'nama_dokumen' => $request->nama_dokumen,
                'tgl_dokumen' => now()->format('Y-m-d'),
                'filepath' => $filePath,
                'dari_pengajuan' => 0,
                'kode_unik' => substr(uniqid(), 0, 20),
                'is_deleted' => 0,
                'created_at' => now()
            ]);

            // Tambahkan hak akses untuk pelihat yang dipilih
            if ($request->has('pelihat') && is_array($request->pelihat)) {
                foreach ($request->pelihat as $idPelihat) {
                    \Illuminate\Support\Facades\DB::table('anggota_dokumen')->insert([
                        'id_dokumen' => $dokumenBaru->id,
                        'id_pengguna' => $idPelihat,
                        'created_at' => now()
                    ]);
                }
            }

            return redirect('/dokumen-terbit')->with('success', 'Dokumen berhasil diunggah dan langsung diterbitkan.');
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Upload Dokumen Langsung Error: ' . $e->getMessage());
            return redirect('/dokumen-terbit')->with('error', 'Terjadi kesalahan saat mengunggah dokumen: ' . $e->getMessage());
        }
    }

    // ini untuk menghapus data dokumen beserta file fisiknya secara permanen dari server (hanya untuk admin)
    public function hapusPermanen($id)
    {
        if (!session()->has('pengguna')) return redirect('/');
        $pengguna = session('pengguna');
        
        if (!$pengguna['is_admin']) {
            return redirect('/dokumen-terbit')->with('error', 'Hanya Admin yang dapat menghapus dokumen permanen.');
        }

        try {
            $dokumen = Dokumen::findOrFail($id);
            $idNomorDokumen = $dokumen->id_nomor_dokumen;

            // Delete physical file
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($dokumen->filepath)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($dokumen->filepath);
            }

            // Hapus relasi
            \Illuminate\Support\Facades\DB::table('anggota_dokumen')->where('id_dokumen', $id)->delete();
            \Illuminate\Support\Facades\DB::table('grup_verifikasi_dokumen')->where('id_dokumen', $id)->delete();

            // Hapus dokumen
            $dokumen->delete();

            // Coba hapus nomor dokumen (jika tidak dipakai dokumen lain)
            try {
                \App\Models\NomorDokumen::where('id', $idNomorDokumen)->delete();
            } catch (\Exception $e) {
                // Biarkan jika gagal (misal constraint)
            }

            return redirect('/dokumen-terbit')->with('success', 'Dokumen berhasil dihapus secara permanen.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Hapus Dokumen Error: ' . $e->getMessage());
            return redirect('/dokumen-terbit')->with('error', 'Gagal menghapus dokumen: ' . $e->getMessage());
        }
    }
}
