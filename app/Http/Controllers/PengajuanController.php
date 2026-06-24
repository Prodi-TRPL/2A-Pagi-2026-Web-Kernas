<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TemplateSurat;
use App\Models\Pengajuan;
use App\Models\GrupVerifikasi;
use PhpOffice\PhpWord\TemplateProcessor;

class PengajuanController extends Controller
{
    public function index()
    {
        if (!session()->has('pengguna')) return redirect('/');

        $pengguna = session('pengguna');
        
        // Hanya verifikator dan admin yang boleh akses halaman ini
        if (!$pengguna['is_admin'] && !$pengguna['is_verifikator']) {
            return redirect('/dashboard');
        }

        // Tampilkan hanya pengajuan milik user yang sedang login
        $pengajuans = Pengajuan::where('id_pengguna', $pengguna['id'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($p) {
                return [
                    'id' => $p->id,
                    'judul' => $p->judul,
                    'tipe' => $p->tipe,
                    'status' => $p->status,
                    'tgl_masuk' => $p->created_at ? $p->created_at->format('d/m/Y') : '-',
                    'raw_tgl_masuk' => $p->created_at ? $p->created_at->format('Y-m-d') : '',
                ];
            });

        return view('pengajuan-surat', compact('pengajuans'));
    }

    public function create()
    {
        if (!session()->has('pengguna')) return redirect('/');
        
        $templates = TemplateSurat::where('is_aktif', 1)->get();
        // Mengambil semua grup verifikasi kecuali Ad-Hoc
        $grupVerifikasi = GrupVerifikasi::where('nama_grup', 'not like', 'Ad-Hoc:%')->get();
        
        return view('pengajuan-baru', compact('templates', 'grupVerifikasi'));
    }

    public function formContohSuratSatu()
    {
        if (!session()->has('pengguna')) return redirect('/');
        
        $template = TemplateSurat::where('nama_template', 'LIKE', '%SK%')->orWhere('filepath', 'LIKE', '%contoh_surat_satu%')->first();
        if (!$template) {
            return redirect('/pengajuan/baru')->with('error', 'Template Contoh Surat Satu tidak ditemukan.');
        }

        $verifikators = \App\Models\Pengguna::has('grupVerifikasi')->get();
        return view('forms.contoh-surat-satu', compact('template', 'verifikators'));
    }

    public function storeContohSuratSatu(Request $request)
    {
        if (!session()->has('pengguna')) return redirect('/');

        $request->validate([
            'id_template' => 'required|exists:template_surat,id',
            'judul' => 'required|string|max:200',
            'verifikator' => 'required|array|min:1|max:3',
            'verifikator.0' => 'required|exists:pengguna,id',
            'NOMOR_SURAT' => 'nullable|string',
            'TANGGAL_SURAT' => 'required|string',
            'HAL' => 'required|string',
            'NAMA_PENGUSUL' => 'required|string',
            'diusulkan' => 'required|array|min:1',
            'diusulkan.*.NAMA_DIUSUL' => 'required|string',
            'diusulkan.*.NIP_DIUSUL' => 'required|string',
            'diusulkan.*.JABATAN_DIUSUL' => 'required|string',
            'file_lampiran' => 'nullable|array',
            'file_lampiran.*' => 'file|max:10240',
        ]);

        $template = TemplateSurat::findOrFail($request->id_template);
        $pengguna = session('pengguna');

        // Buat Grup Verifikasi Ad-Hoc
        $grupVerifikasi = GrupVerifikasi::create([
            'nama_grup' => 'Ad-Hoc: ' . substr($request->judul, 0, 50) . ' (' . time() . ')',
            'id_pengguna' => $pengguna['id'],
            'is_deleted' => 0
        ]);

        // Masukkan pengguna ke anggota grup sesuai urutan yang dipilih
        // array_filter menghapus value kosong dari select opsional
        $selectedVerifikators = array_unique(array_filter($request->verifikator));
        
        foreach ($selectedVerifikators as $verifId) {
            \Illuminate\Support\Facades\DB::table('anggota_grup_verifikasi')->insert([
                'id_grup_verifikasi' => $grupVerifikasi->id,
                'id_pengguna' => $verifId,
                'created_at' => now(),
            ]);
        }

        // Upload Lampiran jika ada
        $adaLampiran = 0;
        if ($request->hasFile('file_lampiran')) {
            $adaLampiran = 1;
        }

        $pengajuan = Pengajuan::create([
            'id_pengguna' => $pengguna['id'],
            'id_grup_verifikasi_verifikator' => $grupVerifikasi->id,
            'judul' => $request->judul,
            'tipe' => $template->tipe,
            'status' => 'POSTED',
            'daftar_menimbang' => '',
            'daftar_memperhatikan' => '',
            'daftar_memutuskan' => '',
            'ada_lampiran' => $adaLampiran,
            'filepath_lampiran' => null, // Sekarang disimpan di lampiran_pengajuans
            'nomor_diusulkan' => $request->NOMOR_SURAT,
        ]);

        if ($request->hasFile('file_lampiran')) {
            foreach ($request->file('file_lampiran') as $file) {
                $filenameLampiran = 'lampiran_' . time() . '_' . $file->getClientOriginalName();
                $file->storeAs('lampiran_pengajuan', $filenameLampiran, 'public');
                
                \Illuminate\Support\Facades\DB::table('lampiran_pengajuan')->insert([
                    'id_pengajuan' => $pengajuan->id,
                    'nama_file' => $file->getClientOriginalName(),
                    'filepath' => 'lampiran_pengajuan/' . $filenameLampiran,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        $templatePath = storage_path('app/public/' . $template->filepath);
        if (!file_exists($templatePath)) {
            $pengajuan->delete();
            return back()->with('error', 'File template surat tidak ditemukan di server.');
        }

        try {
            $templateProcessor = new TemplateProcessor($templatePath);
            
            // Hitung otomatis jumlah lampiran
            $jumlahLampiranInt = $request->hasFile('file_lampiran') ? count($request->file('file_lampiran')) : 0;
            $teksLampiran = '-';
            if ($jumlahLampiranInt > 0) {
                $formatter = new \NumberFormatter('id', \NumberFormatter::SPELLOUT);
                $terbilang = ucfirst($formatter->format($jumlahLampiranInt));
                $teksLampiran = $jumlahLampiranInt . ' (' . $terbilang . ') Berkas';
            }
            
            // Injeksi data ke placeholder
            $templateProcessor->setValue('NOMOR_SURAT', $request->NOMOR_SURAT ?? '');
            $templateProcessor->setValue('TANGGAL_SURAT', \Carbon\Carbon::parse($request->TANGGAL_SURAT)->translatedFormat('d F Y'));
            $templateProcessor->setValue('JUMLAH-LAMPIRAN', $teksLampiran);
            $templateProcessor->setValue('HAL', $request->HAL);
            $templateProcessor->setValue('NAMA_PENGUSUL', $request->NAMA_PENGUSUL);
            
            // Injeksi data tabel dinamis (clone row)
            $templateProcessor->cloneRowAndSetValues('NAMA_DIUSUL', $request->diusulkan);
            
            // Generate filename
            $filename = 'pengajuan_' . $pengajuan->id . '_' . time() . '.docx';
            $saveDir = storage_path('app/public/pengajuan');
            if (!file_exists($saveDir)) {
                mkdir($saveDir, 0755, true);
            }

            $templateProcessor->saveAs($saveDir . '/' . $filename);

            $pengajuan->update([
                'filepath' => 'pengajuan/' . $filename
            ]);

            return redirect('/pengajuan/' . $pengajuan->id . '/edit')->with('success', 'Pengajuan berhasil dibuat! Silakan tinjau draf dokumen Anda.');

        } catch (\Exception $e) {
            $pengajuan->delete();
            return back()->with('error', 'Gagal memproses template: ' . $e->getMessage());
        }
    }

    public function editDoc($id)
    {
        if (!session()->has('pengguna')) return redirect('/');
        
        $pengajuan = Pengajuan::findOrFail($id);

        $pengguna = session('pengguna');
        $isProposer = ($pengajuan->id_pengguna == $pengguna['id']);
        $isAdmin = ($pengguna['is_admin'] == 1);
        $isVerifier = ($pengguna['is_verifikator'] == 1 && !$isAdmin);

        $canEdit = false;
        if ($pengajuan->status === 'POSTED') {
            if ($isVerifier && !$isProposer) {
                return redirect('/dashboard')->with('error', 'Dokumen ini masih dalam tahap penyusunan dan belum bisa ditinjau.');
            }
            if ($isProposer || $isAdmin) {
                $canEdit = true;
            }
        } elseif ($pengajuan->status === 'REJECTED') {
            // Sesuai permintaan: Surat yang ditolak tidak bisa diedit lagi
            $canEdit = false;
        } elseif ($pengajuan->status === 'REJECTED_BY_VERIFIER') {
            if ($isAdmin) {
                $canEdit = true;
            }
        }

        // Konfigurasi ONLYOFFICE mirip dengan TemplateSuratController
        $url = asset('storage/' . $pengajuan->filepath);
        $url = str_replace(['localhost', '127.0.0.1'], 'host.docker.internal', $url);
        
        $callbackUrl = asset('onlyoffice/callback/pengajuan/' . $pengajuan->id);
        $callbackUrl = str_replace(['localhost', '127.0.0.1'], 'host.docker.internal', $callbackUrl);

        $config = [
            "document" => [
                "fileType" => "docx",
                "key" => "pengajuan_" . $pengajuan->id . "_" . time(),
                "title" => "Draf_" . $pengajuan->judul . ".docx",
                "url" => $url,
                "permissions" => [
                    "edit" => $canEdit
                ]
            ],
            "documentType" => "word",
            "editorConfig" => [
                "mode" => $canEdit ? "edit" : "view",
                "lang" => "id",
                "user" => [
                    "id" => (string) session('pengguna.id'),
                    "name" => session('pengguna.nama')
                ],
                "customization" => [
                    "autosave" => true,
                    "forcesave" => true,
                ],
                "callbackUrl" => $callbackUrl,
            ],
            "height" => "100%"
        ];

        $secret = env('ONLYOFFICE_JWT_SECRET', 'polibatam_secret_jwt_key_256bit_2026');
        if (class_exists(\Firebase\JWT\JWT::class)) {
            $config['token'] = \Firebase\JWT\JWT::encode($config, $secret, 'HS256');
        }

        return view('pengajuan-edit', compact('pengajuan', 'config', 'isProposer', 'isAdmin', 'isVerifier'));
    }

    public function kirimVerifikasi($id)
    {
        if (!session()->has('pengguna')) return redirect('/');
        
        $pengajuan = Pengajuan::findOrFail($id);
        
        if ($pengajuan->status !== 'POSTED') {
            return back()->with('error', 'Dokumen tidak dapat dikirim karena status saat ini: ' . $pengajuan->status);
        }

        // Pengusul menyimpan draf
        return redirect('/dashboard')->with('success', 'Draf dokumen berhasil disimpan!');
    }

    public function adminKirimVerifikator($id)
    {
        if (!session()->has('pengguna')) return redirect('/');
        
        $pengguna = session('pengguna');
        if (!$pengguna['is_admin']) {
            return redirect('/dashboard')->with('error', 'Hanya Admin yang dapat mengirim dokumen ke Verifikator.');
        }

        $pengajuan = Pengajuan::findOrFail($id);
        
        if ($pengajuan->status !== 'POSTED') {
            return back()->with('error', 'Dokumen tidak dapat dikirim karena status saat ini: ' . $pengajuan->status);
        }

        $verifikatorPertama = \Illuminate\Support\Facades\DB::table('anggota_grup_verifikasi')
            ->where('id_grup_verifikasi', $pengajuan->id_grup_verifikasi_verifikator)
            ->orderBy('id', 'asc')
            ->first();

        $pengajuan->update([
            'status' => 'REVIEWED',
            'urutan_antrian' => 0,
            'id_verifikator_sekarang' => $verifikatorPertama ? $verifikatorPertama->id_pengguna : null
        ]);

        return redirect('/dashboard')->with('success', 'Dokumen berhasil dikirim dan kini siap ditinjau oleh Verifikator!');
    }

    public function adminKirimUlangVerifikator($id)
    {
        if (!session()->has('pengguna')) return redirect('/');
        $pengguna = session('pengguna');
        if (!$pengguna['is_admin']) return redirect('/dashboard')->with('error', 'Hanya Admin.');

        $pengajuan = Pengajuan::findOrFail($id);
        if ($pengajuan->status !== 'REJECTED_BY_VERIFIER') return back();

        // Kembali ke status REVIEWED tanpa mengubah urutan antrean
        $pengajuan->update(['status' => 'REVIEWED']);

        return redirect('/dashboard')->with('success', 'Dokumen berhasil dikirim ulang ke Verifikator.');
    }

    public function adminKembalikanPengusul(Request $request, $id)
    {
        if (!session()->has('pengguna')) return redirect('/');
        $pengguna = session('pengguna');
        if (!$pengguna['is_admin']) return redirect('/dashboard')->with('error', 'Hanya Admin.');

        $pengajuan = Pengajuan::findOrFail($id);
        if (!in_array($pengajuan->status, ['POSTED', 'REJECTED_BY_VERIFIER'])) return back();

        $pengajuan->update([
            'status' => 'REJECTED',
            'id_verifikator_sekarang' => null,
            'catatan' => $request->catatan ?? $pengajuan->catatan
        ]);

        return redirect('/dashboard')->with('success', 'Dokumen dikembalikan ke Pengusul untuk direvisi.');
    }

    public function terimaPengajuan($id)
    {
        if (!session()->has('pengguna')) return redirect('/');
        
        $pengguna = session('pengguna');
        if (!$pengguna['is_verifikator']) {
            return redirect('/dashboard')->with('error', 'Anda tidak memiliki hak akses untuk menyetujui dokumen ini.');
        }

        $pengajuan = Pengajuan::findOrFail($id);
        
        if ($pengajuan->status !== 'REVIEWED') {
            return back()->with('error', 'Hanya dokumen berstatus REVIEWED yang dapat disetujui.');
        }

        $verifikators = \Illuminate\Support\Facades\DB::table('anggota_grup_verifikasi')
            ->where('id_grup_verifikasi', $pengajuan->id_grup_verifikasi_verifikator)
            ->orderBy('id', 'asc')
            ->get();
        
        $nextUrutan = $pengajuan->urutan_antrian + 1;
        
        if ($nextUrutan < count($verifikators)) {
            $pengajuan->update([
                'urutan_antrian' => $nextUrutan,
                'id_verifikator_sekarang' => $verifikators[$nextUrutan]->id_pengguna
            ]);
            return redirect('/dashboard')->with('success', 'Dokumen disetujui dan diteruskan ke Verifikator selanjutnya.');
        } else {
            $pengajuan->update([
                'status' => 'PUBLISHED',
                'id_verifikator_sekarang' => null
            ]);

            // === INJEKSI QR CODE ===
            try {
                // Siapkan data QR Code
                $namaVerifikator = $pengguna['nama'];
                $grupVerifikator = $pengajuan->grupVerifikator->nama_grup ?? 'Verifikator';
                $judulSurat = $pengajuan->judul;
                $tanggal = now()->translatedFormat('d F Y H:i:s');
                
                $qrContent = "Ditandatangani oleh: {$namaVerifikator}\nGrup: {$grupVerifikator}\nDokumen: {$judulSurat}\nTanggal: {$tanggal}";
                
                // Pastikan direktori ada
                $pengajuanDir = storage_path('app/public/pengajuan');
                if (!is_dir($pengajuanDir)) {
                    mkdir($pengajuanDir, 0755, true);
                }

                // Simpan gambar QR sementara
                $qrFileName = 'qr_temp_' . time() . '_' . uniqid() . '.png';
                $qrPath = $pengajuanDir . '/' . $qrFileName;
                
                $qrOptions = new \chillerlan\QRCode\QROptions([
                    'outputType' => \chillerlan\QRCode\QRCode::OUTPUT_IMAGE_PNG,
                    'eccLevel' => \chillerlan\QRCode\QRCode::ECC_L,
                    'scale' => 4,
                    'imageBase64' => false,
                ]);
                (new \chillerlan\QRCode\QRCode($qrOptions))->render($qrContent, $qrPath);

                // Path ke file dokumen word
                $docPath = storage_path('app/public/' . $pengajuan->filepath);
                
                if (file_exists($docPath)) {
                    // Buka template
                    $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($docPath);
                    
                    // Isi nama dan NIP verifikator
                    $templateProcessor->setValue('NAMA_VERIFIKATOR', $namaVerifikator);
                    $templateProcessor->setValue('NIP_VERIFIKATOR', $pengguna['nip'] ?? '-');
                    
                    // Coba timpa placeholder ${KODE_QR} jika ada
                    try {
                        $templateProcessor->setImageValue('KODE_QR', [
                            'path' => $qrPath,
                            'width' => 100,
                            'height' => 100,
                            'ratio' => false
                        ]);
                        
                        // Save kembali menimpa file aslinya
                        $templateProcessor->saveAs($docPath);
                    } catch (\Exception $ex) {
                        // Jika placeholder ${KODE_QR} tidak ditemukan, templateProcessor mungkin error
                        // Tapi pastikan saveAs tetap jalan untuk teks NAMA & NIP
                        $templateProcessor->saveAs($docPath);
                        \Illuminate\Support\Facades\Log::warning('Placeholder ${KODE_QR} tidak ditemukan: ' . $ex->getMessage());
                    }
                }
                
                // Hapus QR Code temp
                if (file_exists($qrPath)) {
                    @unlink($qrPath);
                }
            } catch (\Exception $e) {
                // Jangan batalkan proses walau QR gagal
                \Illuminate\Support\Facades\Log::error('Gagal injeksi QR Code: ' . $e->getMessage());
            }

            return redirect('/dashboard')->with('success', 'Dokumen berhasil disetujui sepenuhnya dan telah ditandatangani.');
        }
    }

    public function tolakPengajuan(Request $request, $id)
    {
        if (!session()->has('pengguna')) return redirect('/');
        
        $pengguna = session('pengguna');
        if (!$pengguna['is_verifikator']) {
            return redirect('/dashboard')->with('error', 'Anda tidak memiliki hak akses untuk menolak dokumen ini.');
        }

        $request->validate([
            'catatan' => 'required|string'
        ]);

        $pengajuan = Pengajuan::findOrFail($id);
        
        if ($pengajuan->status !== 'REVIEWED') {
            return back()->with('error', 'Hanya dokumen berstatus REVIEWED yang dapat ditolak.');
        }

        $pengajuan->update([
            'status' => 'REJECTED_BY_VERIFIER',
            'catatan' => $request->catatan
        ]);

        return redirect('/dashboard')->with('success', 'Dokumen ditolak dan dikembalikan ke Admin.');
    }

    public function viewLampiran($id)
    {
        if (!session()->has('pengguna')) return redirect('/');
        $pengguna = session('pengguna');
        
        $pengajuan = Pengajuan::findOrFail($id);
        
        $isAdmin = $pengguna['is_admin'];
        $isProposer = ($pengajuan->id_pengguna == $pengguna['id']);
        
        if (!$isAdmin && !$isProposer) {
            $isVerifier = \Illuminate\Support\Facades\DB::table('anggota_grup_verifikasi')
                ->where('id_grup_verifikasi', $pengajuan->id_grup_verifikasi_verifikator)
                ->where('id_pengguna', $pengguna['id'])
                ->exists();
                
            if (!$isVerifier) {
                return redirect('/dashboard')->with('error', 'Anda tidak memiliki akses ke lampiran ini.');
            }
        }
        
        $lampirans = \Illuminate\Support\Facades\DB::table('lampiran_pengajuan')
            ->where('id_pengajuan', $id)
            ->orderBy('id', 'asc')
            ->get();
            
        return view('pengajuan-lampiran', compact('pengajuan', 'lampirans'));
    }

    public function hapusPengajuan(Request $request, $id)
    {
        if (!session()->has('pengguna')) return redirect('/');
        $pengguna = session('pengguna');
        
        $pengajuan = Pengajuan::findOrFail($id);
        
        // Hanya pembuat yang bisa menghapus
        if ($pengajuan->id_pengguna !== $pengguna['id']) {
            return back()->with('error', 'Anda tidak memiliki hak akses untuk menghapus dokumen ini.');
        }

        // Hanya bisa dihapus jika statusnya DRAFT, POSTED, atau REJECTED
        if (!in_array($pengajuan->status, ['DRAFT', 'POSTED', 'REJECTED'])) {
            return back()->with('error', 'Dokumen ini sedang diproses atau sudah selesai, sehingga tidak dapat dihapus.');
        }

        $pengajuan->update(['is_deleted' => 1]);

        return redirect('/dashboard')->with('success', 'Dokumen berhasil dihapus dari sistem.');
    }
}
