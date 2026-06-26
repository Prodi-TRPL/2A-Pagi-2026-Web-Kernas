<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dokumen;
use App\Models\Pengajuan;
use App\Models\TemplateSurat;
use Carbon\Carbon;

class DashboardController extends Controller
{
    // (alur data: fungsi ini mengambil data menggunakan model dokumen dan pengajuan. data hasil query ini akan dikirim dan digunakan di view 'dashboard' pada baris 5 dan 302)
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
            // Admin sama sekali tidak melihat DRAFT dan REJECTED di Dashboard
            // (Dokumen REJECTED milik admin sendiri hanya akan muncul di tab Pengajuan Surat)
            $pengajuanQuery->whereNotIn('status', ['Draf', 'Ditolak Admin']);
        } else {
            // Filter Dokumen user / group (idk how this works but it works)
            $dokumenQuery->where(function ($q) use ($id) {
                $q->where('id_pengguna', $id)
                  ->orWhereHas('anggotaDokumen', fn($a) => $a->where('id_pengguna', $id))
                  ->orWhereHas('grupVerifikasiDokumen.pengguna', fn($a) => $a->where('id_pengguna', $id));
            });

            // Filter Pengajuan ( pengaju / verifikator / member)
            $pengajuanQuery->where(function ($q) use ($id) {
                // Pengusul bisa melihat semua status pengajuan mereka
                $q->where('id_pengguna', $id)
                  ->orWhere(function ($q2) use ($id) {
                      $q2->where('status', 'Diproses Admin')
                         ->whereHas('anggotaPengajuan', fn($a) => $a->where('id_pengguna', $id));
                  })
                  // Verifikator hanya bisa melihat dokumen JIKA giliran mereka (id_verifikator_sekarang)
                  // ATAU dokumen sudah selesai (PUBLISHED/REJECTED) dan mereka termasuk dalam grup verifikator
                  ->orWhere('id_verifikator_sekarang', $id)
                  ->orWhere(function ($q2) use ($id) {
                      $q2->whereIn('status', ['Diterbitkan', 'Ditolak Admin'])
                         ->where(function ($q3) use ($id) {
                             $q3->whereHas('grupVerifikator.pengguna', fn($a) => $a->where('id_pengguna', $id))
                                ->orWhereHas('grupVerifikasiPengajuan.pengguna', fn($a) => $a->where('id_pengguna', $id));
                         });
                  });
            });
        }

        // digunakan dalam statistika 
        $stats = [
            'total_sk' => (clone $dokumenQuery)->where('tipe', 'SK')->count(),
            'total_st' => (clone $dokumenQuery)->where('tipe', 'ST')->count(),
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

        // Data grafik 7 hari tekhir
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
        // Membagi berdasarkan tipe (menggunakan IN array untuk mencakup tipe-tipe spesifik)
        $templatesArahan = $templates->whereIn('tipe', ['Arahan', 'SK', 'ST', 'SE', 'Instruksi', 'SOP']);
        $templatesKorespondensi = $templates->whereIn('tipe', ['Korespondensi', 'Surat Dinas', 'Nota Dinas', 'Surat Edaran', 'Surat Undangan']);
        $templatesKhusus = $templates->whereIn('tipe', ['Khusus', 'MoU', 'Perjanjian', 'Surat Kuasa', 'Berita Acara']);

        return view('dashboard', compact('user', 'stats', 'pengajuanTerbaru', 'dokumenTerbaru', 'chartData', 'templatesArahan', 'templatesKorespondensi', 'templatesKhusus'));
    }

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
                  ->orWhereHas('grupVerifikasiDokumen.pengguna', fn($a) => $a->where('id_pengguna', $id));
            });
        }

        $dokumen = $dokumenQuery
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($d) {
                return [
                    'id' => $d->id,
                    'judul' => $d->nama_dokumen ?? 'Dokumen Tanpa Judul',
                    'tipe' => $d->tipe,
                    'updated_at' => $d->tgl_dokumen ?? $d->created_at,
                    'pengajuan_id' => $d->dari_pengajuan
                ];
            });

        return view('dokumen-terbit', compact('dokumen'));
    }
}
