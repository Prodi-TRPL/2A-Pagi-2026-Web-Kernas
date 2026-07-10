<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dokumen;
use App\Models\Pengajuan;
use App\Models\TemplateSurat;
use Carbon\Carbon;

class DashboardController extends Controller
{
    // ini untuk mengambil data statistik, dokumen, dan pengajuan dari model terkait lalu mengirimkannya ke view 'dashboard'
    public function index()
    {
        if (!session()->has('pengguna')) {
            return redirect('/');
        }

        $user = session('pengguna');
        $isAdmin = $user['is_admin'] ?? 0;
        $id = $user['id'];

        // Query Dokumen
        $dokumenQuery = Dokumen::where('is_deleted', 0);
        
        // Query Pengajuan
        $pengajuanQuery = Pengajuan::where('is_deleted', 0)->where('status', '!=', 'Diterbitkan');

        if ($isAdmin) {
            // Admin hanya melihat dokumen yang memerlukan tindakannya (Diproses Admin atau Revisi)
            $pengajuanQuery->whereIn('status', ['Diproses Admin', 'Revisi']);
        } else {
            // Filter Dokumen user / group (idk how this works but it works)
            $dokumenQuery->where(function ($q) use ($id) {
                $q->where('id_pengguna', $id)
                  ->orWhereHas('anggotaDokumen', fn($a) => $a->where('id_pengguna', $id))
                  ->orWhereHas('grupVerifikasiDokumen', fn($a) => $a->where('pengguna.id', $id));
            });

            // Filter Pengajuan - Dashboard HANYA menampilkan dokumen yang butuh verifikasi user saat ini
            $pengajuanQuery->where('id_verifikator_sekarang', $id);
        }

        // digunakan dalam statistika 
        $stats = [
            'total_arahan' => (clone $dokumenQuery)->where('tipe', 'Naskah Dinas Arahan')->count(),
            'total_korespondensi' => (clone $dokumenQuery)->where('tipe', 'Naskah Dinas Korespondensi')->count(),
            'total_khusus' => (clone $dokumenQuery)->where('tipe', 'Naskah Dinas Khusus')->count(),
            'pengajuan_proses' => (clone $pengajuanQuery)->whereIn('status', ['Diproses Admin', 'Menunggu Verifikasi'])->count(),
            'pengajuan_hari' => (clone $pengajuanQuery)->whereDate('created_at', Carbon::today())->count(),
        ];

        //  Pengajuan baru
        $pengajuanTerbaru = (clone $pengajuanQuery)
            ->with(['pengaju', 'grupVerifikator'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($p) use ($id) {
                return [
                    'id' => $p->id,
                    'urutan_antrian' => $p->urutan_antrian ?? $p->id,
                    'judul' => $p->judul,
                    'jenis' => $p->tipe,
                    'pengaju' => $p->pengaju->nama ?? '-',
                    'tgl_masuk' => \Carbon\Carbon::parse($p->created_at)->translatedFormat('d M Y'),
                    'raw_tgl_masuk' => \Carbon\Carbon::parse($p->created_at)->format('Y-m-d'),
                    'status' => $p->status,
                    'status' => $p->status,
                    'grup_verifikasi' => $p->grupVerifikator->nama_grup ?? '-',
                    'can_delete' => $p->id_pengguna == $id && in_array($p->status, ['Draf', 'Diproses Admin', 'Ditolak Admin']),
                ];
            });

        // tabel dokumen
        $dokumenTerbaru = (clone $dokumenQuery)
            ->with(['pembuat', 'nomorDokumen'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($d) {
                return [
                    'id' => $d->id,
                    'jenis' => $d->tipe,
                    'judul' => $d->nama_dokumen ?? $d->judul ?? 'Dokumen Tanpa Judul',
                    'nomor' => $d->nomorDokumen->nomor_terformat ?? '-',
                    'tgl_terbit' => \Carbon\Carbon::parse($d->tgl_dokumen ?? $d->created_at)->translatedFormat('d M Y'),
                    'dibuat_oleh' => $d->pembuat->nama ?? 'Sistem',
                ];
            });

        // Data grafik 6-7 hari tekhir
        $chartMasuk = [];
        $chartKeluar = [];
        $chartLabels = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $chartLabels[] = $date->format('d M');
            
            $chartMasuk[] = (clone $pengajuanQuery)->whereDate('created_at', $date)->count();
            $chartKeluar[] = (clone $dokumenQuery)->whereDate('created_at', $date)->count();
        }

        $chartData = [
            'labels' => $chartLabels,
            'masuk' => $chartMasuk,
            'keluar' => $chartKeluar
        ];

        // Ambil template surat aktif untuk dropdown
        $templates = TemplateSurat::where('is_aktif', 1)->get();
        // Membagi berdasarkan tipe
        $templatesArahan = $templates->where('tipe', 'Naskah Dinas Arahan');
        $templatesKorespondensi = $templates->where('tipe', 'Naskah Dinas Korespondensi');
        $templatesKhusus = $templates->where('tipe', 'Naskah Dinas Khusus');

        return view('dashboard', compact('user', 'stats', 'pengajuanTerbaru', 'dokumenTerbaru', 'chartData', 'templatesArahan', 'templatesKorespondensi', 'templatesKhusus'));
    }

    // ini untuk mengambil daftar dokumen yang sudah diterbitkan berdasarkan hak akses pengguna dan mengirimkannya ke view 'dokumen-terbit'
    public function dokumenTerbit()
    {
        if (!session()->has('pengguna')) return redirect('/');

        $user = session('pengguna');
        $isAdmin = $user['is_admin'] ?? 0;
        $id = $user['id'];

        $dokumenQuery = Dokumen::where('is_deleted', 0);

        if (!$isAdmin) {
            $dokumenQuery->where(function ($q) use ($id) {
                // Pembuat dokumen
                $q->where('id_pengguna', $id)
                  // Anggota dokumen
                  ->orWhereHas('anggotaDokumen', fn($a) => $a->where('id_pengguna', $id))
                  // Verifikator (dari grup_verifikasi_dokumen)
                  ->orWhereHas('grupVerifikasiDokumen', fn($a) => $a->where('pengguna.id', $id));
            });
        }

        $dokumen = $dokumenQuery
            ->with(['nomorDokumen', 'pembuat'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($d) {
                return [
                    'id' => $d->id,
                    'nomor' => $d->nomorDokumen->nomor_terformat ?? '-',
                    'judul' => $d->nama_dokumen ?? 'Dokumen Tanpa Judul',
                    'tipe' => $d->tipe,
                    'filepath' => $d->filepath,
                    'updated_at' => $d->tgl_dokumen ?? $d->created_at,
                    'pengajuan_id' => $d->dari_pengajuan,
                    'nama_pengusul' => $d->pembuat->nama ?? '-',
                    'unit_pengusul' => $d->pembuat->unit ?? '-'
                ];
            });

        $semuaPengguna = \App\Models\Pengguna::select('id', 'nama', 'nip', 'is_admin')->where('is_deleted', 0)->orderBy('nama', 'asc')->get();

        return view('dokumen-terbit', compact('dokumen', 'semuaPengguna'));
    }
}
