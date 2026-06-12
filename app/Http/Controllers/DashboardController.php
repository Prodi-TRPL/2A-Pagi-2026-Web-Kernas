<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dokumen;
use App\Models\Pengajuan;
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
        $pengajuanQuery = Pengajuan::where('is_deleted', 0);

        if (!$isAdmin) {
            // Filter Dokumen user / group (idk how this works but it works)
            $dokumenQuery->where(function ($q) use ($id) {
                $q->whereHas('anggotaDokumen', fn($a) => $a->where('id_pengguna', $id))
                  ->orWhereHas('grupVerifikasiDokumen.pengguna', fn($a) => $a->where('id_pengguna', $id));
            });

            // Filter Pengajuan ( pengaju / verifikator / member)(dont tuch)
            $pengajuanQuery->where(function ($q) use ($id) {
                $q->where('id_pengguna', $id)
                  ->orWhereHas('anggotaPengajuan', fn($a) => $a->where('id_pengguna', $id))
                  ->orWhereHas('grupVerifikator.pengguna', fn($a) => $a->where('id_pengguna', $id))
                  ->orWhereHas('grupVerifikasiPengajuan.pengguna', fn($a) => $a->where('id_pengguna', $id));
            });
        }

        // digunakan dalam grafik nanti
        $stats = [
            'total_sk' => (clone $dokumenQuery)->where('tipe', 'SK')->count(),
            'total_st' => (clone $dokumenQuery)->where('tipe', 'ST')->count(),
            'pengajuan_proses' => (clone $pengajuanQuery)->whereIn('status', ['POSTED', 'APPROVED'])->count(),
            'pengajuan_hari' => (clone $pengajuanQuery)->whereDate('created_at', Carbon::today())->count(),
        ];

        //  Pengajuan baru
        $pengajuanTerbaru = (clone $pengajuanQuery)
            ->with(['pengaju', 'grupVerifikator'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($p) {
                return [
                    'id' => $p->id,
                    'urutan_antrian' => $p->urutan_antrian ?? $p->id,
                    'judul' => $p->judul,
                    'jenis' => $p->tipe,
                    'pengaju' => $p->pengaju->nama ?? '-',
                    'tgl_masuk' => \Carbon\Carbon::parse($p->created_at)->translatedFormat('d M Y'),
                    'raw_tgl_masuk' => \Carbon\Carbon::parse($p->created_at)->format('Y-m-d'),
                    'status' => $p->status,
                    'grup_verifikasi' => $p->grupVerifikator->nama_grup ?? '-',
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

        return view('dashboard', compact('user', 'stats', 'pengajuanTerbaru', 'dokumenTerbaru', 'chartData'));
    }
}
