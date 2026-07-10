<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\TemplateSurat;
use App\Models\Pengajuan;
use App\Models\GrupVerifikasi;
use PhpOffice\PhpWord\TemplateProcessor;

class PengajuanController extends Controller
{
    // ini untuk menampilkan daftar pengajuan surat (berdasarkan akses pengguna) ke view 'pengajuan-surat'
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
            ->where('status', '!=', 'Diterbitkan')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($p) {
                return [
                    'id' => $p->id,
                    'judul' => $p->judul,
                    'tipe' => $p->tipe,
                    'status' => $p->status,
                    'status' => $p->status,
                    'tgl_masuk' => $p->created_at ? $p->created_at->format('d/m/Y') : '-',
                    'raw_tgl_masuk' => $p->created_at ? $p->created_at->format('Y-m-d') : '',
                ];
            });

        // Ambil template surat aktif untuk dropdown (fitur "Buat Pengajuan Baru" untuk Admin & Verifikator)
        $templates = \App\Models\TemplateSurat::where('is_aktif', 1)->get();
        $templatesArahan = $templates->filter(function($tmpl) { return str_contains($tmpl->tipe, 'Naskah Dinas Arahan'); });
        $templatesKorespondensi = $templates->filter(function($tmpl) { return str_contains($tmpl->tipe, 'Naskah Dinas Korespondensi'); });
        $templatesKhusus = $templates->filter(function($tmpl) { return str_contains($tmpl->tipe, 'Naskah Dinas Khusus'); });

        return view('pengajuan-surat', compact('pengajuans', 'templatesArahan', 'templatesKorespondensi', 'templatesKhusus'));
    }

    // ini untuk menampilkan halaman pilih jenis pengajuan baru
    public function create()
    {
        if (!session()->has('pengguna')) return redirect('/');
        
        $templates = TemplateSurat::where('is_aktif', 1)->get();
        // Mengambil semua grup verifikasi kecuali Ad-Hoc
        $grupVerifikasi = GrupVerifikasi::where('nama_grup', 'not like', 'Ad-Hoc:%')->get();
        
        return view('pengajuan-baru', compact('templates', 'grupVerifikasi'));
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
        if ($pengajuan->status === 'Diproses Admin') {
            if ($isVerifier && !$isProposer) {
                return redirect('/dashboard')->with('error', 'Dokumen ini masih dalam tahap penyusunan dan belum bisa ditinjau.');
            }
            if ($isProposer || $isAdmin) {
                $canEdit = true;
            }
        } elseif ($pengajuan->status === 'Ditolak Admin') {
            // Sesuai permintaan: Surat yang ditolak tidak bisa diedit lagi
            $canEdit = false;
        } elseif ($pengajuan->status === 'Revisi') {
            if ($isAdmin) {
                $canEdit = true;
            }
        }

        // Konfigurasi ONLYOFFICE mirip dengan TemplateSuratController
        $url = asset('storage/' . $pengajuan->filepath);
        $url = str_replace(['localhost', '127.0.0.1'], 'host.docker.internal', $url);
        
        $callbackUrl = asset('onlyoffice/callback/pengajuan/' . $pengajuan->id);
        $callbackUrl = str_replace(['localhost', '127.0.0.1'], 'host.docker.internal', $callbackUrl);

        $fileType = pathinfo($pengajuan->filepath, PATHINFO_EXTENSION);
        $fileType = strtolower($fileType) ?: 'docx';

        $config = [
            "document" => [
                "fileType" => $fileType,
                "key" => "pengajuan_" . $pengajuan->id . "_" . time(),
                "title" => "Draf_" . $pengajuan->judul . "." . $fileType,
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

        $secret = env('ONLYOFFICE_JWT_SECRET');
        if (!empty($secret) && class_exists(\Firebase\JWT\JWT::class)) {
            $config['token'] = \Firebase\JWT\JWT::encode($config, $secret, 'HS256');
        }

        $riwayat = \App\Models\RiwayatPengajuan::with('pengguna')
            ->where('id_pengajuan', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        $verifikator1 = \App\Models\Pengguna::where('is_deleted', 0)->whereHas('grupVerifikasi', function($q) {
            $q->where('tingkat', '1');
        })->get();
        $verifikator2 = \App\Models\Pengguna::where('is_deleted', 0)->whereHas('grupVerifikasi', function($q) {
            $q->where('tingkat', '2');
        })->get();
        $verifikator3 = \App\Models\Pengguna::where('is_deleted', 0)->whereHas('grupVerifikasi', function($q) {
            $q->where('tingkat', '3');
        })->get();

        return view('pengajuan-edit', compact('pengajuan', 'config', 'isProposer', 'isAdmin', 'isVerifier', 'riwayat', 'verifikator1', 'verifikator2', 'verifikator3'));
    }

    // ini untuk mengirim pengajuan (draft) agar mulai diproses oleh Admin
    public function kirimVerifikasi($id)
    {
        if (!session()->has('pengguna')) return redirect('/');
        
        $pengajuan = Pengajuan::findOrFail($id);
        
        if ($pengajuan->status !== 'Diproses Admin') {
            return back()->with('error', 'Dokumen tidak dapat dikirim karena status saat ini: ' . $pengajuan->status);
        }

        // Pengusul menyimpan draf
        return redirect('/dashboard')->with('success', 'Draf dokumen berhasil disimpan!');
    }

    // ini untuk diproses oleh admin: mengirim dokumen pengajuan ke verifikator tingkat selanjutnya
    public function adminKirimVerifikator(Request $request, $id)
    {
        if (!session()->has('pengguna')) return redirect('/');
        
        $pengguna = session('pengguna');
        if (!$pengguna['is_admin']) {
            return redirect('/dashboard')->with('error', 'Hanya Admin yang dapat mengirim dokumen ke Verifikator.');
        }

        $pengajuan = Pengajuan::findOrFail($id);
        
        if ($pengajuan->status !== 'Diproses Admin') {
            return back()->with('error', 'Dokumen tidak dapat dikirim karena status saat ini: ' . $pengajuan->status);
        }

        $request->validate([
            'nomor_surat' => 'required|string|max:100',
            'verifikator' => 'required|array|min:1|max:3',
            'verifikator.0' => 'required|exists:pengguna,id',
        ]);
        
        // Simpan nomor yang diinput Admin ke dalam dokumen menggunakan PHPWord
        $docPath = storage_path('app/public/' . $pengajuan->filepath);
        if (file_exists($docPath)) {
            try {
                $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($docPath);
                $templateProcessor->setValue('NOMOR_SURAT', htmlspecialchars(trim($request->nomor_surat ?? '')));
                $templateProcessor->saveAs($docPath);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('Gagal menyisipkan nomor surat: ' . $e->getMessage());
            }
        }

        $selectedVerifikators = array_values(array_unique(array_filter($request->verifikator)));
        
        \Illuminate\Support\Facades\DB::table('grup_verifikasi_pengajuan')->where('id_pengajuan', $pengajuan->id)->delete();

        foreach ($selectedVerifikators as $index => $verifId) {
            \Illuminate\Support\Facades\DB::table('grup_verifikasi_pengajuan')->insert([
                'id_pengajuan' => $pengajuan->id,
                'id_pengguna' => $verifId,
                'urutan' => $index,
                'created_at' => now(),
            ]);
        }

        $verifikatorPertamaId = $selectedVerifikators[0] ?? null;

        $pengajuan->update([
            'status' => 'Menunggu Verifikasi',
            'urutan_antrian' => 0,
            'id_grup_verifikasi_verifikator' => null,
            'id_verifikator_sekarang' => $verifikatorPertamaId,
            'nomor_diusulkan' => $request->nomor_surat
        ]);

        \App\Models\RiwayatPengajuan::create([
            'id_pengajuan' => $pengajuan->id,
            'id_pengguna' => $pengguna['id'],
            'aksi' => 'Diteruskan ke Verifikator',
            'catatan_aksi' => 'Admin mengirim dokumen ke Antrian Verifikasi',
            'versi' => 1,
            'created_at' => now()
        ]);

        return redirect('/dashboard')->with('success', 'Dokumen berhasil dikirim dan kini siap ditinjau oleh Verifikator!');
    }

    // ini untuk diproses oleh admin: mengirim ulang dokumen pengajuan ke verifikator jika ada perbaikan
    public function adminKirimUlangVerifikator($id)
    {
        if (!session()->has('pengguna')) return redirect('/');
        $pengguna = session('pengguna');
        if (!$pengguna['is_admin']) return redirect('/dashboard')->with('error', 'Hanya Admin.');

        $pengajuan = Pengajuan::findOrFail($id);
        if ($pengajuan->status !== 'Revisi') return back();

        // Kembali ke status REVIEWED tanpa mengubah urutan antrean
        $pengajuan->update(['status' => 'Menunggu Verifikasi']);

        \App\Models\RiwayatPengajuan::create([
            'id_pengajuan' => $pengajuan->id,
            'id_pengguna' => $pengguna['id'],
            'aksi' => 'Diteruskan ke Verifikator',
            'catatan_aksi' => 'Admin mengirim ulang dokumen ke Verifikator (Re-Review)',
            'versi' => 1,
            'created_at' => now()
        ]);

        return redirect('/dashboard')->with('success', 'Dokumen berhasil dikirim ulang ke Verifikator.');
    }

    // ini untuk diproses oleh admin: mengembalikan dokumen ke pengusul untuk direvisi
    public function adminKembalikanPengusul(Request $request, $id)
    {
        if (!session()->has('pengguna')) return redirect('/');
        $pengguna = session('pengguna');
        if (!$pengguna['is_admin']) return redirect('/dashboard')->with('error', 'Hanya Admin.');

        $pengajuan = Pengajuan::findOrFail($id);
        if (!in_array($pengajuan->status, ['Diproses Admin', 'Revisi'])) return back();

        $pengajuan->update([
            'status' => 'Ditolak Admin',
            'id_verifikator_sekarang' => null,
            'catatan' => $request->catatan ?? $pengajuan->catatan
        ]);

        \App\Models\RiwayatPengajuan::create([
            'id_pengajuan' => $pengajuan->id,
            'id_pengguna' => $pengguna['id'],
            'aksi' => 'DITOLAK',
            'catatan_aksi' => 'Admin mengembalikan dokumen ke Pengusul. Catatan: ' . ($request->catatan ?? $pengajuan->catatan),
            'versi' => 1,
            'created_at' => now()
        ]);

        return redirect('/dashboard')->with('success', 'Dokumen dikembalikan ke Pengusul untuk direvisi.');
    }

    // ini untuk menangani persetujuan (acc) dokumen oleh verifikator atau penyelesaian dokumen menjadi Terbit oleh admin
    public function terimaPengajuan($id)
    {
        if (!session()->has('pengguna')) return redirect('/');
        
        $pengguna = session('pengguna');
        if (!$pengguna['is_verifikator']) {
            return redirect('/dashboard')->with('error', 'Anda tidak memiliki hak akses untuk menyetujui dokumen ini.');
        }

        $pengajuan = Pengajuan::findOrFail($id);
        
        if ($pengajuan->status !== 'Menunggu Verifikasi') {
            return back()->with('error', 'Hanya dokumen berstatus REVIEWED yang dapat disetujui.');
        }

        $verifikators = \Illuminate\Support\Facades\DB::table('grup_verifikasi_pengajuan')
            ->where('id_pengajuan', $pengajuan->id)
            ->orderBy('urutan', 'asc')
            ->get();
        
        $nextUrutan = $pengajuan->urutan_antrian + 1;
        
        if ($nextUrutan < count($verifikators)) {
            $pengajuan->update([
                'urutan_antrian' => $nextUrutan,
                'id_verifikator_sekarang' => $verifikators[$nextUrutan]->id_pengguna
            ]);

            \App\Models\RiwayatPengajuan::create([
                'id_pengajuan' => $pengajuan->id,
                'id_pengguna' => $pengguna['id'],
                'aksi' => 'DISETUJUI',
                'catatan_aksi' => 'Verifikator menyetujui dokumen (menunggu verifikator selanjutnya)',
                'versi' => 1,
                'created_at' => now()
            ]);

            return redirect('/dashboard')->with('success', 'Dokumen disetujui dan diteruskan ke Verifikator selanjutnya.');
        } else {
            $pengajuan->update([
                'status' => 'Diterbitkan',
                'id_verifikator_sekarang' => null
            ]);

            // === INJEKSI QR CODE ===
            try {
                // Generate Nomor Dokumen terlebih dahulu agar bisa masuk ke QR Code
                $tahun = date('Y');
                $lastNomor = \App\Models\NomorDokumen::where('tipe', $pengajuan->tipe)
                                ->where('tahun', $tahun)
                                ->orderBy('urutan', 'desc')
                                ->first();
                $urutan = $lastNomor ? $lastNomor->urutan + 1 : 1;
                
                // Gunakan nomor surat usulan Admin jika tersedia, jika tidak buat otomatis
                $nomorTerformat = !empty($pengajuan->nomor_diusulkan) 
                                    ? $pengajuan->nomor_diusulkan 
                                    : sprintf('%03d/%s/%d', $urutan, $pengajuan->tipe, $tahun);

                // Siapkan data QR Code
                $namaVerifikator = $pengguna['nama'];
                $grupVerifikator = $pengajuan->grupVerifikator->nama_grup ?? 'Verifikator';
                $judulSurat = $pengajuan->judul;
                $tanggal = now()->translatedFormat('d F Y H:i:s');
                
                $jabatanVerifikator = $pengguna['jabatan'] ?? '-';
                $nipVerifikator = $pengguna['nip'] ?? '-';
                $qrContent = "Ditandatangani oleh: {$namaVerifikator}\nNIP: {$nipVerifikator}\nJabatan: {$jabatanVerifikator}\nGrup: {$grupVerifikator}\nNomor: {$nomorTerformat}\nDokumen: {$judulSurat}\nTanggal: {$tanggal}";
                
                // Pastikan direktori ada
                $pengajuanDir = storage_path('app/public/pengajuan');
                if (!is_dir($pengajuanDir)) {
                    mkdir($pengajuanDir, 0755, true);
                }

                // Simpan gambar QR sementara
                $qrFileName = 'qr_temp_' . time() . '_' . uniqid() . '.png';
                $qrPath = $pengajuanDir . '/' . $qrFileName;
                
                $qrOptions = new \chillerlan\QRCode\QROptions([
                    'outputInterface' => \chillerlan\QRCode\Output\QRGdImagePNG::class,
                    'eccLevel' => \chillerlan\QRCode\Common\EccLevel::H,
                    'scale' => 8,
                    'imageBase64' => false,
                ]);
                (new \chillerlan\QRCode\QRCode($qrOptions))->render($qrContent, $qrPath);
                
                // === Sisipkan Logo ke Tengah QR Code ===
                $logoPath = public_path('images/logo_polibatam.png');
                if (file_exists($logoPath) && file_exists($qrPath)) {
                    $qrImage = imagecreatefrompng($qrPath);
                    $logo = imagecreatefrompng($logoPath);
                    
                    imagealphablending($qrImage, true);
                    imagesavealpha($qrImage, true);
                    
                    $QR_width = imagesx($qrImage);
                    $QR_height = imagesy($qrImage);
                    $logo_width = imagesx($logo);
                    $logo_height = imagesy($logo);
                
                    // Skala logo menjadi sekitar 30% lebar QR Code
                    $logo_qr_width = (int) ($QR_width / 3); 
                    $scale = $logo_width / $logo_qr_width;
                    $logo_qr_height = (int) ($logo_height / $scale);
                
                    // Hitung posisi tengah
                    $x = (int) (($QR_width - $logo_qr_width) / 2);
                    $y = (int) (($QR_height - $logo_qr_height) / 2);
                
                    // Merge image
                    imagecopyresampled(
                        $qrImage, $logo, 
                        $x, $y, 
                        0, 0, 
                        $logo_qr_width, $logo_qr_height, 
                        $logo_width, $logo_height
                    );
                    
                    // Save kembali QR Code yang sudah ada logo
                    imagepng($qrImage, $qrPath);
                    imagedestroy($logo);
                    imagedestroy($qrImage);
                }

                // Path ke file dokumen word
                $docPath = storage_path('app/public/' . $pengajuan->filepath);
                
                if (file_exists($docPath)) {
                    // Buka template
                    $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($docPath);
                    
                    // Isi nama dan NIP verifikator
                    $templateProcessor->setValue('NAMA_VERIFIKATOR', $namaVerifikator);
                    $templateProcessor->setValue('NIP_VERIFIKATOR', $pengguna['nip'] ?? '-');
                    $templateProcessor->setValue('JABATAN_VERIFIKATOR', $pengguna['jabatan'] ?? '-');
                    $templateProcessor->setValue('TANGGAL_SURAT', now()->translatedFormat('d F Y'));
                    
                    // Coba timpa placeholder ${KODE_QR} jika ada
                    try {
                        $templateProcessor->setImageValue('KODE_QR', [
                            'path' => $qrPath,
                            'width' => 100,
                            'height' => 100,
                            'ratio' => false
                        ]);
                    } catch (\Exception $ex) {}
                    
                    // Coba timpa placeholder ${QR_CODE} jika ada
                    try {
                        $templateProcessor->setImageValue('QR_CODE', [
                            'path' => $qrPath,
                            'width' => 100,
                            'height' => 100,
                            'ratio' => false
                        ]);
                    } catch (\Exception $ex) {}

                    // Save kembali menimpa file aslinya
                    $templateProcessor->saveAs($docPath);
                }
                
                // Hapus QR Code temp
                if (file_exists($qrPath)) {
                    @unlink($qrPath);
                }
                
                // === KONVERSI PDF (ONLYOFFICE) ===
                try {
                    $fileUrl = asset('storage/' . $pengajuan->filepath);
                    $fileUrl = str_replace(['localhost', '127.0.0.1'], 'host.docker.internal', $fileUrl);
                    
                    // Coba fallback URL Laragon jika menggunakan host.docker.internal
                    if (str_contains(public_path(), 'laragon\www') || str_contains(public_path(), 'laragon/www')) {
                        $publicPath = str_replace('\\', '/', public_path());
                        $laragonWww = str_replace('\\', '/', 'C:/laragon/www');
                        if (str_starts_with($publicPath, $laragonWww)) {
                            $relativePath = substr($publicPath, strlen($laragonWww));
                            $fileUrl = 'http://host.docker.internal' . str_replace(' ', '%20', $relativePath) . '/storage/' . str_replace(' ', '%20', $pengajuan->filepath);
                        }
                    }

                    $payload = [
                        'async' => false,
                        'filetype' => 'docx',
                        'key' => 'publish_' . $pengajuan->id . '_' . time(),
                        'outputtype' => 'pdf',
                        'url' => $fileUrl
                    ];
                    
                    $headers = [];
                    $secret = env('ONLYOFFICE_JWT_SECRET');
                    if (!empty($secret) && class_exists(\Firebase\JWT\JWT::class)) {
                        $token = \Firebase\JWT\JWT::encode($payload, $secret, 'HS256');
                        $payload['token'] = $token;
                        $headers['Authorization'] = 'Bearer ' . $token;
                    }

                    $response = \Illuminate\Support\Facades\Http::timeout(8)
                                    ->withHeaders($headers)
                                    ->post('http://localhost:8080/ConvertService.ashx', $payload);
                    
                    if ($response->successful()) {
                        $xml = simplexml_load_string($response->body());
                        if (isset($xml->FileUrl)) {
                            $pdfUrl = (string) $xml->FileUrl;
                            $pdfUrl = str_replace('172.17.0.2', '127.0.0.1', $pdfUrl);
                            $pdfUrl = preg_replace('/^http:\/\/[^\/]+/', 'http://127.0.0.1:8080', $pdfUrl);

                            $pdfData = file_get_contents($pdfUrl);
                            if ($pdfData !== false) {
                                $pdfFilepath = str_replace('.docx', '.pdf', $pengajuan->filepath);
                                \Illuminate\Support\Facades\Storage::disk('public')->put($pdfFilepath, $pdfData);
                                
                                // Timpa filepath dengan versi PDF yang sudah dibuat
                                $pengajuan->update(['filepath' => $pdfFilepath]);
                            }
                        } else {
                            \Illuminate\Support\Facades\Log::error('ONLYOFFICE Convert Error: ' . $response->body());
                        }
                    }
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('ONLYOFFICE Convert Exception: ' . $e->getMessage());
                }
                
                // Record Nomor Dokumen menggunakan variabel yang sudah digenerate di atas
                $nomorDokumen = \App\Models\NomorDokumen::create([
                    'tipe' => $pengajuan->tipe,
                    'tahun' => $tahun,
                    'urutan' => $urutan,
                    'nomor_terformat' => $nomorTerformat,
                    'created_at' => now(),
                ]);

                // === BUAT RECORD DOKUMEN ===
                $dokumenBaru = \App\Models\Dokumen::create([
                    'id_nomor_dokumen' => $nomorDokumen->id,
                    'tgl_dokumen' => now()->format('Y-m-d'),
                    'id_pengguna' => $pengajuan->id_pengguna,
                    'tipe' => $pengajuan->tipe,
                    'nama_dokumen' => $pengajuan->judul,
                    'filepath' => $pengajuan->filepath, // sudah jadi pdf
                    'catatan' => $pengajuan->catatan,
                    'dari_pengajuan' => $pengajuan->id,
                    'kode_unik' => substr(uniqid(), 0, 20),
                    'is_deleted' => 0
                ]);
                
                // Pindahkan anggota pengajuan menjadi anggota dokumen
                $anggotaPengajuan = \Illuminate\Support\Facades\DB::table('anggota_pengajuan')
                                    ->where('id_pengajuan', $pengajuan->id)
                                    ->get();
                foreach ($anggotaPengajuan as $angg) {
                    \Illuminate\Support\Facades\DB::table('anggota_dokumen')->insert([
                        'id_dokumen' => $dokumenBaru->id,
                        'id_pengguna' => $angg->id_pengguna,
                        'created_at' => now()
                    ]);
                }
                
                // Pindahkan grup verifikasi pengajuan menjadi grup verifikasi dokumen
                $grupVerifikasi = \Illuminate\Support\Facades\DB::table('grup_verifikasi_pengajuan')
                                    ->where('id_pengajuan', $pengajuan->id)
                                    ->get();
                foreach ($grupVerifikasi as $gv) {
                    \Illuminate\Support\Facades\DB::table('grup_verifikasi_dokumen')->insert([
                        'id_dokumen' => $dokumenBaru->id,
                        'id_pengguna' => $gv->id_pengguna,
                        'urutan' => $gv->urutan,
                        'created_at' => now()
                    ]);
                }
                
            } catch (\Exception $e) {
                // Jangan batalkan proses walau QR/Konversi gagal (ini MUNGKIN terjadi)
                \Illuminate\Support\Facades\Log::error('Gagal injeksi QR / Konversi PDF: ' . $e->getMessage());
            }

            \App\Models\RiwayatPengajuan::create([
                'id_pengajuan' => $pengajuan->id,
                'id_pengguna' => $pengguna['id'],
                'aksi' => 'DITERBITKAN',
                'catatan_aksi' => 'Verifikator akhir menyetujui dokumen dan dokumen resmi diterbitkan',
                'versi' => 1,
                'created_at' => now()
            ]);

            return redirect('/dashboard')->with('success', 'Dokumen berhasil disetujui sepenuhnya dan telah ditandatangani.');
        }
    }

    // ini untuk menangani penolakan dokumen pengajuan oleh verifikator atau admin (dikembalikan ke tahap sebelumnya)
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
        
        if ($pengajuan->status !== 'Menunggu Verifikasi') {
            return back()->with('error', 'Hanya dokumen berstatus REVIEWED yang dapat ditolak.');
        }

        $pengajuan->update([
            'status' => 'Revisi',
            'catatan' => $request->catatan
        ]);

        \App\Models\RiwayatPengajuan::create([
            'id_pengajuan' => $pengajuan->id,
            'id_pengguna' => $pengguna['id'],
            'aksi' => 'DITOLAK',
            'catatan_aksi' => 'Verifikator menolak dokumen. Catatan: ' . $request->catatan,
            'versi' => 1,
            'created_at' => now()
        ]);

        return redirect('/dashboard')->with('success', 'Dokumen ditolak dan dikembalikan ke Admin.');
    }
    // ini untuk menampilkan daftar lampiran yang ada pada suatu pengajuan
    public function viewLampiran($id)
    {
        if (!session()->has('pengguna')) return redirect('/');
        $pengguna = session('pengguna');
        
        $pengajuan = Pengajuan::findOrFail($id);
        
        $isAdmin = $pengguna['is_admin'];
        $isProposer = ($pengajuan->id_pengguna == $pengguna['id']);
        
        if (!$isAdmin && !$isProposer) {
            // Fix IDOR: jika id_grup_verifikasi_verifikator NULL, query akan return false
            // sehingga non-admin/non-pemilik tidak bisa akses saat belum ada verifikator
            $isVerifier = \Illuminate\Support\Facades\DB::table('grup_verifikasi_pengajuan')
                ->where('id_pengajuan', $pengajuan->id)
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

    /**
     * Download individual lampiran melalui controller (terproteksi auth + kepemilikan so dont worry hehe).
     * Mencegah akses publik langsung via /storage/lampiran_pengajuan/...
     */
    // ini untuk mengunduh file lampiran dari suatu pengajuan
    public function unduhLampiran($pengajuanId, $lampiranId)
    {
        if (!session()->has('pengguna')) return redirect('/');
        $pengguna = session('pengguna');

        $pengajuan = Pengajuan::findOrFail($pengajuanId);

        $isAdmin    = $pengguna['is_admin'];
        $isProposer = ($pengajuan->id_pengguna == $pengguna['id']);

        if (!$isAdmin && !$isProposer) {
            $isVerifier = \Illuminate\Support\Facades\DB::table('grup_verifikasi_pengajuan')
                ->where('id_pengajuan', $pengajuan->id)
                ->where('id_pengguna', $pengguna['id'])
                ->exists();
            if (!$isVerifier) {
                abort(403, 'Anda tidak memiliki akses ke lampiran ini.');
            }
        }

        $lampiran = \Illuminate\Support\Facades\DB::table('lampiran_pengajuan')
            ->where('id', $lampiranId)
            ->where('id_pengajuan', $pengajuanId)
            ->first();

        if (!$lampiran) abort(404);

        // Validasi path aman (harus dalam direktori lampiran_pengajuan)
        $filePath = storage_path('app/public/' . $lampiran->filepath);
        $allowed  = storage_path('app/public/lampiran_pengajuan');
        if (!str_starts_with(realpath($filePath) ?: '', realpath($allowed))) {
            abort(403);
        }

        if (!file_exists($filePath)) abort(404);

        if (request()->has('download')) {
            return response()->download($filePath, $lampiran->nama_file, [
                'Content-Type'        => 'application/pdf',
                'X-Content-Type-Options' => 'nosniff',
            ]);
        }
        
        return response()->file($filePath, [
            'Content-Type'        => 'application/pdf',
            'X-Content-Type-Options' => 'nosniff',
            'Content-Disposition' => 'inline; filename="' . addslashes($lampiran->nama_file) . '"'
        ]);
    }
     // jlogic penghapusan surat (self explenatory)
    // ini untuk menghapus data pengajuan (soft delete) jika statusnya masih mengizinkan untuk dihapus
    public function hapusPengajuan(Request $request, $id)
    {
        if (!session()->has('pengguna')) return redirect('/');
        $pengguna = session('pengguna');
        
        $pengajuan = Pengajuan::findOrFail($id);
        
        // Hanya pembuat yang bisa menghapus
        if ($pengajuan->id_pengguna !== $pengguna['id']) {
            return back()->with('error', 'Anda tidak memiliki hak akses untuk menghapus dokumen ini.');
        }

        // Hanya bisa dihapus jika statusnya DRAFT, POSTED, atau REJECTED (agar tidak ada "security problem")
        if (!in_array($pengajuan->status, ['Draf', 'Diproses Admin', 'Ditolak Admin'])) {
            return back()->with('error', 'Dokumen ini sedang diproses atau sudah selesai, sehingga tidak dapat dihapus.');
        }

        $pengajuan->update(['is_deleted' => 1]);

        return redirect('/dashboard')->with('success', 'Dokumen berhasil dihapus dari sistem.');
    }

    // =========================================================================
    // ATTTTEENTIONNNNN kode yang dibawah ini hanya digunakan untuk surat arahan dinas
    // =========================================================================

    public function formArahanEdaran()
    {
        if (!session()->has('pengguna')) return redirect('/');
        $template = \App\Models\TemplateSurat::where('filepath', 'LIKE', '%edaran%')->first();
        if (!$template) return redirect('/pengajuan/baru')->with('error', 'Template Surat Edaran tidak ditemukan.');
        return view('forms.arahan-edaran', compact('template'));
    }

    public function storeArahanEdaran(\Illuminate\Http\Request $request)
    {
        if (!session()->has('pengguna')) return redirect('/');
        $template = \App\Models\TemplateSurat::findOrFail($request->id_template);
        $pengguna = session('pengguna');
        $adaLampiran = $request->hasFile('file_lampiran') ? 1 : 0;

        $pengajuan = \App\Models\Pengajuan::create([
            'id_pengguna' => $pengguna['id'],
            'id_grup_verifikasi_verifikator' => null,
            'judul' => $request->judul,
            'tipe' => $template->tipe,
            'status' => 'Diproses Admin',
            'ada_lampiran' => $adaLampiran,
            'nomor_diusulkan' => $request->NOMOR_SURAT,
            'daftar_menimbang' => '',
            'daftar_memperhatikan' => '',
            'daftar_memutuskan' => ''
        ]);

        $this->handleLampiran($request, $pengajuan);

        $templatePath = storage_path('app/public/' . $template->filepath);
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);
        
        $templateProcessor->setValue('NOMOR_SURAT', htmlspecialchars(trim($request->NOMOR_SURAT ?? '')) ?: '${NOMOR_SURAT}');
        $templateProcessor->setValue('JUDUL_SURAT', htmlspecialchars(trim($request->JUDUL_SURAT ?? '')));
        $templateProcessor->setValue('TANGGAL_SURAT', \Carbon\Carbon::parse($request->TANGGAL_SURAT)->translatedFormat('d F Y'));
        $templateProcessor->setValue('ISI_SURAT_EDARAN', htmlspecialchars(trim($request->ISI_SURAT_EDARAN ?? '')));
        
        // Dynamic Table for Anggota
        $anggotaVals = [];
        if ($request->has('ANGGOTA_SURAT')) {
            foreach ($request->ANGGOTA_SURAT as $i => $val) {
                $anggotaVals[] = ['NO_ANGGOTA' => ($i+1).'.', 'ANGGOTA_SURAT' => htmlspecialchars($val)];
            }
        }
        if (count($anggotaVals) > 0) $templateProcessor->cloneRowAndSetValues('NO_ANGGOTA', $anggotaVals);
        else { $templateProcessor->setValue('NO_ANGGOTA', ''); $templateProcessor->setValue('ANGGOTA_SURAT', ''); }

        // Dynamic Table for Hukum
        $hukumVals = [];
        if ($request->has('HUKUM')) {
            foreach ($request->HUKUM as $i => $val) {
                $hukumVals[] = ['NO_HUKUM' => ($i+1).'.', 'HUKUM' => htmlspecialchars($val)];
            }
        }
        if (count($hukumVals) > 0) $templateProcessor->cloneRowAndSetValues('NO_HUKUM', $hukumVals);
        else { $templateProcessor->setValue('NO_HUKUM', ''); $templateProcessor->setValue('HUKUM', ''); }

        $filename = 'pengajuan_' . $pengajuan->id . '_' . time() . '.docx';
        $templateProcessor->saveAs(storage_path('app/public/pengajuan/' . $filename));
        $pengajuan->update(['filepath' => 'pengajuan/' . $filename]);

        \App\Models\RiwayatPengajuan::create(['id_pengajuan' => $pengajuan->id, 'id_pengguna' => $pengguna['id'], 'aksi' => 'DIBUAT', 'catatan_aksi' => 'Pengajuan baru dibuat', 'versi' => 1, 'created_at' => now()]);
        return redirect('/pengajuan/' . $pengajuan->id . '/edit')->with('success', 'Pengajuan berhasil dibuat! Silakan tinjau draf dokumen Anda.');
    }

    public function formArahanPerintah()
    {
        if (!session()->has('pengguna')) return redirect('/');
        $template = \App\Models\TemplateSurat::where('filepath', 'LIKE', '%perintah%')->first();
        if (!$template) return redirect('/pengajuan/baru')->with('error', 'Template Surat Perintah tidak ditemukan.');
        return view('forms.arahan-perintah', compact('template'));
    }

    public function storeArahanPerintah(\Illuminate\Http\Request $request)
    {
        if (!session()->has('pengguna')) return redirect('/');
        $template = \App\Models\TemplateSurat::findOrFail($request->id_template);
        $pengguna = session('pengguna');
        $adaLampiran = $request->hasFile('file_lampiran') ? 1 : 0;

        $pengajuan = \App\Models\Pengajuan::create([
            'id_pengguna' => $pengguna['id'],
            'id_grup_verifikasi_verifikator' => null,
            'judul' => $request->judul,
            'tipe' => $template->tipe,
            'status' => 'Diproses Admin',
            'ada_lampiran' => $adaLampiran,
            'nomor_diusulkan' => $request->NOMOR_SURAT,
            'daftar_menimbang' => $request->MENIMBANG_SURAT ?? '',
            'daftar_memperhatikan' => $request->ATAS_DASAR ?? '',
            'daftar_memutuskan' => ''
        ]);

        $this->handleLampiran($request, $pengajuan);

        $templatePath = storage_path('app/public/' . $template->filepath);
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);
        
        $templateProcessor->setValue('NOMOR_SURAT', htmlspecialchars(trim($request->NOMOR_SURAT ?? '')) ?: '${NOMOR_SURAT}');
        $templateProcessor->setValue('TANGGAL_SURAT', \Carbon\Carbon::parse($request->TANGGAL_SURAT)->translatedFormat('d F Y'));
        
        // Multi-line support
        $templateProcessor->setValue('MENIMBANG_SURAT', str_replace("
", "<w:br/>", htmlspecialchars(trim($request->MENIMBANG_SURAT ?? ''))));
        $templateProcessor->setValue('ATAS_DASAR', str_replace("
", "<w:br/>", htmlspecialchars(trim($request->ATAS_DASAR ?? ''))));
        $templateProcessor->setValue('PERINTAH', str_replace("
", "<w:br/>", htmlspecialchars(trim($request->PERINTAH ?? ''))));
        $templateProcessor->setValue('UNTUK', str_replace("
", "<w:br/>", htmlspecialchars(trim($request->UNTUK ?? ''))));

        $filename = 'pengajuan_' . $pengajuan->id . '_' . time() . '.docx';
        $templateProcessor->saveAs(storage_path('app/public/pengajuan/' . $filename));
        $pengajuan->update(['filepath' => 'pengajuan/' . $filename]);

        \App\Models\RiwayatPengajuan::create(['id_pengajuan' => $pengajuan->id, 'id_pengguna' => $pengguna['id'], 'aksi' => 'DIBUAT', 'catatan_aksi' => 'Pengajuan baru dibuat', 'versi' => 1, 'created_at' => now()]);
        return redirect('/pengajuan/' . $pengajuan->id . '/edit')->with('success', 'Pengajuan berhasil dibuat! Silakan tinjau draf dokumen Anda.');
    }

    public function formArahanKeputusan()
    {
        if (!session()->has('pengguna')) return redirect('/');
        $template = \App\Models\TemplateSurat::where('filepath', 'LIKE', '%keputusan%')->first();
        if (!$template) return redirect('/pengajuan/baru')->with('error', 'Template Surat Keputusan tidak ditemukan.');
        $peraturans = \App\Models\Peraturan::orderBy('tahun', 'desc')->orderBy('kode', 'asc')->get();
        return view('forms.arahan-keputusan', compact('template', 'peraturans'));
    }

    public function storeArahanKeputusan(\Illuminate\Http\Request $request)
    {
        if (!session()->has('pengguna')) return redirect('/');
        $template = \App\Models\TemplateSurat::findOrFail($request->id_template);
        $pengguna = session('pengguna');
        $adaLampiran = $request->hasFile('file_lampiran') ? 1 : 0;

        $pengajuan = \App\Models\Pengajuan::create([
            'id_pengguna' => $pengguna['id'],
            'id_grup_verifikasi_verifikator' => null,
            'judul' => $request->judul,
            'tipe' => $template->tipe,
            'status' => 'Diproses Admin',
            'ada_lampiran' => $adaLampiran,
            'nomor_diusulkan' => $request->NOMOR_SURAT,
            'daftar_menimbang' => implode("
", $request->menimbang ?? []),
            'daftar_memperhatikan' => '',
            'daftar_memutuskan' => $request->MEMUTUSKAN ?? ''
        ]);

        $this->handleLampiran($request, $pengajuan);

        $templatePath = storage_path('app/public/' . $template->filepath);
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);
        
        $templateProcessor->setValue('NOMOR_SURAT', htmlspecialchars(trim($request->NOMOR_SURAT ?? '')) ?: '${NOMOR_SURAT}');
        $templateProcessor->setValue('TANGGAL_SURAT', \Carbon\Carbon::parse($request->TANGGAL_SURAT)->translatedFormat('d F Y'));
        $templateProcessor->setValue('TENTANG_SURAT', htmlspecialchars(trim($request->TENTANG_SURAT ?? '')));
        $templateProcessor->setValue('MEMUTUSKAN', htmlspecialchars(trim($request->MEMUTUSKAN ?? '')));

        // Menimbang
        $menimbangText = '';
        if ($request->has('menimbang')) {
            $char = 'a';
            foreach ($request->menimbang as $m) {
                if(trim($m)){ $menimbangText .= $char . '. ' . htmlspecialchars(trim($m)) . '<w:br/>'; $char++; }
            }
        }
        if ($menimbangText) $menimbangText = substr($menimbangText, 0, -8);
        $templateProcessor->setValue('MENIMBANG_SURAT', $menimbangText ?: '${MENIMBANG_SURAT}');

        // Mengingat (Peraturan)
        $mengingatText = '';
        if ($request->has('peraturan')) {
            $peraturans = \App\Models\Peraturan::whereIn('id', $request->peraturan)->get();
            $count = 1;
            foreach ($peraturans as $p) {
                \Illuminate\Support\Facades\DB::table('pengajuan_peraturan')->insert(['id_pengajuan' => $pengajuan->id, 'id_peraturan' => $p->id, 'created_at' => now()]);
                $mengingatText .= $count . '. ' . htmlspecialchars($p->kode . ' tentang ' . $p->judul) . '<w:br/>';
                $count++;
            }
        }
        if ($mengingatText) $mengingatText = substr($mengingatText, 0, -8);
        $templateProcessor->setValue('MENGINGAT', $mengingatText ?: '${MENGINGAT}');

        // Instruksi
        $instruksiVals = [];
        if ($request->has('ISI_INSTRUKSI')) {
            $labels = ['KESATU', 'KEDUA', 'KETIGA', 'KEEMPAT', 'KELIMA', 'KEENAM', 'KETUJUH', 'KEDELAPAN', 'KESEMBILAN', 'KESEPULUH'];
            foreach ($request->ISI_INSTRUKSI as $i => $val) {
                if(trim($val)) {
                    $lbl = $i < count($labels) ? $labels[$i] : 'KESE-'.($i+1);
                    $instruksiVals[] = ['LABEL_INSTRUKSI' => $lbl, 'ISI_INSTRUKSI' => htmlspecialchars($val)];
                }
            }
        }
        if (count($instruksiVals) > 0) $templateProcessor->cloneRowAndSetValues('LABEL_INSTRUKSI', $instruksiVals);
        else { $templateProcessor->setValue('LABEL_INSTRUKSI', ''); $templateProcessor->setValue('ISI_INSTRUKSI', ''); }

        $filename = 'pengajuan_' . $pengajuan->id . '_' . time() . '.docx';
        $templateProcessor->saveAs(storage_path('app/public/pengajuan/' . $filename));
        $pengajuan->update(['filepath' => 'pengajuan/' . $filename]);

        \App\Models\RiwayatPengajuan::create(['id_pengajuan' => $pengajuan->id, 'id_pengguna' => $pengguna['id'], 'aksi' => 'DIBUAT', 'catatan_aksi' => 'Pengajuan baru dibuat', 'versi' => 1, 'created_at' => now()]);
        return redirect('/pengajuan/' . $pengajuan->id . '/edit')->with('success', 'Pengajuan berhasil dibuat! Silakan tinjau draf dokumen Anda.');
    }

    public function formArahanTugasBiasa()
    {
        if (!session()->has('pengguna')) return redirect('/');
        $template = \App\Models\TemplateSurat::where('filepath', 'LIKE', '%tugas_biasa%')->first();
        if (!$template) return redirect('/pengajuan/baru')->with('error', 'Template Surat Tugas Biasa tidak ditemukan.');
        $penggunas = \App\Models\Pengguna::select('id', 'nama', 'nip', 'jabatan')->where('is_deleted', 0)->orderBy('nama')->get();
        return view('forms.arahan-tugas-biasa', compact('template', 'penggunas'));
    }

    public function storeArahanTugasBiasa(\Illuminate\Http\Request $request)
    {
        if (!session()->has('pengguna')) return redirect('/');
        $template = \App\Models\TemplateSurat::findOrFail($request->id_template);
        $pengguna = session('pengguna');
        $adaLampiran = $request->hasFile('file_lampiran') ? 1 : 0;

        $pengajuan = \App\Models\Pengajuan::create([
            'id_pengguna' => $pengguna['id'],
            'id_grup_verifikasi_verifikator' => null,
            'judul' => $request->judul,
            'tipe' => $template->tipe,
            'status' => 'Diproses Admin',
            'ada_lampiran' => $adaLampiran,
            'nomor_diusulkan' => $request->NOMOR_SURAT,
            'daftar_menimbang' => '',
            'daftar_memperhatikan' => '',
            'daftar_memutuskan' => ''
        ]);

        $this->handleLampiran($request, $pengajuan);

        // Save ke anggota_pengajuan
        if ($request->id_pengguna) {
            \Illuminate\Support\Facades\DB::table('anggota_pengajuan')->insert(['id_pengajuan' => $pengajuan->id, 'id_pengguna' => $request->id_pengguna, 'created_at' => now()]);
        }
        
        $pegawai = \App\Models\Pengguna::find($request->id_pengguna);

        $templatePath = storage_path('app/public/' . $template->filepath);
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);
        
        $templateProcessor->setValue('NOMOR_SURAT', htmlspecialchars(trim($request->NOMOR_SURAT ?? '')) ?: '${NOMOR_SURAT}');
        $templateProcessor->setValue('TANGGAL_SURAT', \Carbon\Carbon::parse($request->TANGGAL_SURAT)->translatedFormat('d F Y'));
        $templateProcessor->setValue('KALIMAT_PEMBUKA', htmlspecialchars(trim($request->KALIMAT_PEMBUKA ?? '')));
        $templateProcessor->setValue('NAMA_DIUSUL', htmlspecialchars($pegawai ? $pegawai->nama : ''));
        $templateProcessor->setValue('NIP_DIUSUL', htmlspecialchars($pegawai ? $pegawai->nip : ''));
        $templateProcessor->setValue('JABATAN_DIUSUL', htmlspecialchars($pegawai ? $pegawai->jabatan : ''));
        $templateProcessor->setValue('TUGAS', htmlspecialchars(trim($request->TUGAS ?? '')));
        $templateProcessor->setValue('TANGGAL_TUGAS', htmlspecialchars(trim($request->TANGGAL_TUGAS ?? '')));
        $templateProcessor->setValue('LOKASI_TUGAS', htmlspecialchars(trim($request->LOKASI_TUGAS ?? '')));

        $filename = 'pengajuan_' . $pengajuan->id . '_' . time() . '.docx';
        $templateProcessor->saveAs(storage_path('app/public/pengajuan/' . $filename));
        $pengajuan->update(['filepath' => 'pengajuan/' . $filename]);

        \App\Models\RiwayatPengajuan::create(['id_pengajuan' => $pengajuan->id, 'id_pengguna' => $pengguna['id'], 'aksi' => 'DIBUAT', 'catatan_aksi' => 'Pengajuan baru dibuat', 'versi' => 1, 'created_at' => now()]);
        return redirect('/pengajuan/' . $pengajuan->id . '/edit')->with('success', 'Pengajuan berhasil dibuat! Silakan tinjau draf dokumen Anda.');
    }

    public function formArahanTugasTabel()
    {
        if (!session()->has('pengguna')) return redirect('/');
        $template = \App\Models\TemplateSurat::where('filepath', 'LIKE', '%tugas_tabel%')->first();
        if (!$template) return redirect('/pengajuan/baru')->with('error', 'Template Surat Tugas Tabel tidak ditemukan.');
        $penggunas = \App\Models\Pengguna::select('id', 'nama', 'nip', 'jabatan')->where('is_deleted', 0)->orderBy('nama')->get();
        return view('forms.arahan-tugas-tabel', compact('template', 'penggunas'));
    }

    public function storeArahanTugasTabel(\Illuminate\Http\Request $request)
    {
        if (!session()->has('pengguna')) return redirect('/');
        $template = \App\Models\TemplateSurat::findOrFail($request->id_template);
        $pengguna = session('pengguna');
        $adaLampiran = $request->hasFile('file_lampiran') ? 1 : 0;

        $pengajuan = \App\Models\Pengajuan::create([
            'id_pengguna' => $pengguna['id'],
            'id_grup_verifikasi_verifikator' => null,
            'judul' => $request->judul,
            'tipe' => $template->tipe,
            'status' => 'Diproses Admin',
            'ada_lampiran' => $adaLampiran,
            'nomor_diusulkan' => $request->NOMOR_SURAT,
            'daftar_menimbang' => '',
            'daftar_memperhatikan' => '',
            'daftar_memutuskan' => ''
        ]);

        $this->handleLampiran($request, $pengajuan);

        $diusulkanVals = [];
        if ($request->has('diusulkan')) {
            foreach ($request->diusulkan as $diusul) {
                if (!empty($diusul['id_pengguna'])) {
                    \Illuminate\Support\Facades\DB::table('anggota_pengajuan')->insert(['id_pengajuan' => $pengajuan->id, 'id_pengguna' => $diusul['id_pengguna'], 'created_at' => now()]);
                    $diusulkanVals[] = [
                        'NAMA_DIUSULKAN' => htmlspecialchars($diusul['NAMA_DIUSULKAN'] ?? ''),
                        'NIP_DIUSULKAN' => htmlspecialchars($diusul['NIP_DIUSULKAN'] ?? ''),
                        'JABATAN_DIUSULKAN' => htmlspecialchars($diusul['JABATAN_DIUSULKAN'] ?? '')
                    ];
                }
            }
        }

        $templatePath = storage_path('app/public/' . $template->filepath);
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);
        
        $templateProcessor->setValue('NOMOR_SURAT', htmlspecialchars(trim($request->NOMOR_SURAT ?? '')) ?: '${NOMOR_SURAT}');
        $templateProcessor->setValue('TANGGAL SURAT', \Carbon\Carbon::parse($request->TANGGAL_SURAT)->translatedFormat('d F Y'));
        $templateProcessor->setValue('KALIMAT_PEMBUKA', htmlspecialchars(trim($request->KALIMAT_PEMBUKA ?? '')));
        $templateProcessor->setValue('UNTUK_SURAT', htmlspecialchars(trim($request->UNTUK_SURAT ?? '')));
        $templateProcessor->setValue('TANGGAL_PENUGASAN', htmlspecialchars(trim($request->TANGGAL_PENUGASAN ?? '')));
        $templateProcessor->setValue('LOKASI_PENUGASAN', htmlspecialchars(trim($request->LOKASI_PENUGASAN ?? '')));
        
        if (count($diusulkanVals) > 0) $templateProcessor->cloneRowAndSetValues('NAMA_DIUSULKAN', $diusulkanVals);
        else { 
            $templateProcessor->setValue('NAMA_DIUSULKAN', ''); 
            $templateProcessor->setValue('NIP_DIUSULKAN', ''); 
            $templateProcessor->setValue('JABATAN_DIUSULKAN', ''); 
        }

        $filename = 'pengajuan_' . $pengajuan->id . '_' . time() . '.docx';
        $templateProcessor->saveAs(storage_path('app/public/pengajuan/' . $filename));
        $pengajuan->update(['filepath' => 'pengajuan/' . $filename]);

        \App\Models\RiwayatPengajuan::create(['id_pengajuan' => $pengajuan->id, 'id_pengguna' => $pengguna['id'], 'aksi' => 'DIBUAT', 'catatan_aksi' => 'Pengajuan baru dibuat', 'versi' => 1, 'created_at' => now()]);
        return redirect('/pengajuan/' . $pengajuan->id . '/edit')->with('success', 'Pengajuan berhasil dibuat! Silakan tinjau draf dokumen Anda.');
    }

    // Helper untuk menangani unggahan lampiran secara umum
    private function handleLampiran($request, $pengajuan)
    {
        if ($request->hasFile('file_lampiran')) {
            foreach ($request->file('file_lampiran') as $file) {
                $handle = fopen($file->getRealPath(), 'rb');
                $magic  = fread($handle, 4);
                fclose($handle);
                if ($magic !== '%PDF') continue;
                
                $namaAsli = preg_replace('/[^\w\-\.\s]/', '', $file->getClientOriginalName());
                $filenameSimpan = 'lmp_' . $pengajuan->id . '_' . \Illuminate\Support\Str::uuid() . '.pdf';
                $file->storeAs('lampiran_pengajuan', $filenameSimpan, 'public');
                \Illuminate\Support\Facades\DB::table('lampiran_pengajuan')->insert([
                    'id_pengajuan' => $pengajuan->id, 'nama_file' => $namaAsli, 'filepath' => 'lampiran_pengajuan/' . $filenameSimpan, 'created_at' => now(), 'updated_at' => now()
                ]);
            }
        }
    }
    // ini untuk menampilkan form pengajuan surat dengan template SK/ST beserta pilihan verifikator
    public function formContohSuratSatu()
    {
        if (!session()->has('pengguna')) return redirect('/');
        
        $template = TemplateSurat::where('nama_template', 'LIKE', '%SK%')->orWhere('filepath', 'LIKE', '%contoh_surat_satu%')->first();
        if (!$template) {
            return redirect('/pengajuan/baru')->with('error', 'Template Contoh Surat Satu tidak ditemukan.');
        }

        $penggunas = \App\Models\Pengguna::select('id', 'nama', 'nip', 'jabatan')->where('is_deleted', 0)->orderBy('nama')->get();

        $verifikator1 = \App\Models\Pengguna::select('id', 'nama', 'nip', 'jabatan')->where('is_deleted', 0)->whereHas('grupVerifikasi', function($q) {
            $q->where('tingkat', '1');
        })->get();
        $verifikator2 = \App\Models\Pengguna::select('id', 'nama', 'nip', 'jabatan')->where('is_deleted', 0)->whereHas('grupVerifikasi', function($q) {
            $q->where('tingkat', '2');
        })->get();
        $verifikator3 = \App\Models\Pengguna::select('id', 'nama', 'nip', 'jabatan')->where('is_deleted', 0)->whereHas('grupVerifikasi', function($q) {
            $q->where('tingkat', '3');
        })->get();
        
        $peraturans = \App\Models\Peraturan::orderBy('tahun', 'desc')->orderBy('kode', 'asc')->get();

        return view('forms.contoh-surat-satu', compact('template', 'penggunas', 'verifikator1', 'verifikator2', 'verifikator3', 'peraturans'));
    }

    // ini untuk memproses data dari form pengajuan SK/ST, membuat file dokumen docx dari template, dan menyimpannya ke database
    public function storeContohSuratSatu(Request $request)
    {
        if (!session()->has('pengguna')) return redirect('/');

        $request->validate([
            'id_template' => 'required|exists:template_surat,id',
            'judul' => 'required|string|max:200',
            'NOMOR_SURAT' => 'nullable|string',
            'TANGGAL_SURAT' => 'required|string',
            'HAL' => 'required|string',
            'NAMA_PENGUSUL' => 'required|string',
            'diusulkan' => 'required|array|min:1',
            'diusulkan.*.id_pengguna' => 'required|exists:pengguna,id',
            'diusulkan.*.NAMA_DIUSUL' => 'required|string',
            'diusulkan.*.NIP_DIUSUL' => 'required|string',
            'diusulkan.*.JABATAN_DIUSUL' => 'required|string',
            'file_lampiran' => 'nullable|array|max:10',
            'file_lampiran.*' => 'file|max:5120|mimes:pdf|mimetypes:application/pdf',
        ]);

        $template = TemplateSurat::findOrFail($request->id_template);
        $pengguna = session('pengguna');

        // Upload Lampiran jika ada
        $adaLampiran = 0;
        if ($request->hasFile('file_lampiran')) {
            $adaLampiran = 1;
        }

        $pengajuan = Pengajuan::create([
            'id_pengguna' => $pengguna['id'],
            'id_grup_verifikasi_verifikator' => null,
            'judul' => $request->judul,
            'tipe' => $template->tipe,
            'status' => 'Diproses Admin',
            'daftar_menimbang' => '',
            'daftar_memperhatikan' => '',
            'daftar_memutuskan' => '',
            'ada_lampiran' => $adaLampiran,
            'filepath_lampiran' => null, // Sekarang disimpan di lampiran_pengajuans
            'nomor_diusulkan' => $request->NOMOR_SURAT,
        ]);

        if ($request->hasFile('file_lampiran')) {
            foreach ($request->file('file_lampiran') as $file) {
                // === VALIDASI MAGIC BYTES PDF ===
                // Cek 4 byte pertama isi file harus '%PDF' agar file tidak bisa dipalsukan
                $handle = fopen($file->getRealPath(), 'rb');
                $magic  = fread($handle, 4);
                fclose($handle);
                if ($magic !== '%PDF') {
                    // Hapus pengajuan yang sudah dibuat agar tidak orphan
                    $pengajuan->delete();
                    return back()->with('error', 'File lampiran yang diunggah bukan file PDF yang valid.');
                }

                // Nama asli disimpan di DB untuk tampilan (sudah disanitasi)
                $namaAsli       = preg_replace('/[^\w\-\.\s]/', '', $file->getClientOriginalName());
                // Nama penyimpanan pakai UUID aman, bukan nama dari user
                $filenameSimpan = 'lmp_' . $pengajuan->id . '_' . Str::uuid() . '.pdf';

                $file->storeAs('lampiran_pengajuan', $filenameSimpan, 'public');

                \Illuminate\Support\Facades\DB::table('lampiran_pengajuan')->insert([
                    'id_pengajuan' => $pengajuan->id,
                    'nama_file'   => $namaAsli,
                    'filepath'    => 'lampiran_pengajuan/' . $filenameSimpan,
                    'created_at'  => now(),
                    'updated_at'  => now()
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
            
            $templateProcessor->setValue('NOMOR_SURAT', htmlspecialchars(trim($request->NOMOR_SURAT ?? '')) ?: '${NOMOR_SURAT}');
            $templateProcessor->setValue('TANGGAL_SURAT', \Carbon\Carbon::parse($request->TANGGAL_SURAT)->translatedFormat('d F Y'));
            $templateProcessor->setValue('JUMLAH-LAMPIRAN', $teksLampiran);
            $templateProcessor->setValue('HAL', htmlspecialchars(trim($request->HAL ?? '')));
            $templateProcessor->setValue('NAMA_PENGUSUL', htmlspecialchars(trim($request->NAMA_PENGUSUL ?? '')));

            // Injeksi Dasar Peraturan (Mengingat)
            $mengingatText = '';
            if ($request->has('peraturan')) {
                $peraturanIds = $request->peraturan;
                $peraturans = \App\Models\Peraturan::whereIn('id', $peraturanIds)->get();
                $count = 1;
                foreach ($peraturans as $p) {
                    // Simpan relasi ke pengajuan_peraturan
                    \Illuminate\Support\Facades\DB::table('pengajuan_peraturan')->insert([
                        'id_pengajuan' => $pengajuan->id,
                        'id_peraturan' => $p->id,
                        'created_at' => now(),
                    ]);
                    
                    // Format untuk DOCX
                    $mengingatText .= $count . '. ' . htmlspecialchars($p->kode . ' tentang ' . $p->judul) . '<w:br/>';
                    $count++;
                }
            }
            // Jika ada text, hilangkan <w:br/> terakhir. Jika kosong, biarkan placeholder.
            if ($mengingatText) {
                $mengingatText = substr($mengingatText, 0, -8); 
            }
            $templateProcessor->setValue('MENGINGAT', $mengingatText ?: '${MENGINGAT}');
            
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

            // Save ke anggota_pengajuan
            foreach ($request->diusulkan as $diusul) {
                // $diusul['id_pengguna'] is passed from the form
                if (!empty($diusul['id_pengguna'])) {
                    \Illuminate\Support\Facades\DB::table('anggota_pengajuan')->insert([
                        'id_pengajuan' => $pengajuan->id,
                        'id_pengguna' => $diusul['id_pengguna'],
                        'created_at' => now(),
                    ]);
                }
            }

            // Catat Riwayat
            \App\Models\RiwayatPengajuan::create([
                'id_pengajuan' => $pengajuan->id,
                'id_pengguna' => $pengguna['id'],
                'aksi' => 'DIBUAT',
                'catatan_aksi' => 'Pengajuan baru dibuat',
                'versi' => 1,
                'created_at' => now()
            ]);

            return redirect('/pengajuan/' . $pengajuan->id . '/edit')->with('success', 'Pengajuan berhasil dibuat! Silakan tinjau draf dokumen Anda.');

        } catch (\Exception $e) {
            $pengajuan->delete();
            \Illuminate\Support\Facades\Log::error('Gagal memproses template pengajuan #' . ($pengajuan->id ?? '?') . ': ' . $e->getMessage());
            return back()->with('error', 'Gagal memproses template surat. Silakan coba lagi atau hubungi administrator.');
        }
    }

    // ini untuk menampilkan form pengajuan dokumen dinas/arahan
    public function formDinasArahan()
    {
        if (!session()->has('pengguna')) return redirect('/');
        
        $template = \App\Models\TemplateSurat::where('filepath', 'LIKE', '%dinas_arahan_bentuk_peraturan%')->first();
        if (!$template) {
            return redirect('/pengajuan/baru')->with('error', 'Template Surat Dinas Arahan tidak ditemukan.');
        }

        $peraturans = \App\Models\Peraturan::orderBy('tahun', 'desc')->orderBy('kode', 'asc')->get();

        return view('forms.dinas-arahan', compact('template', 'peraturans'));
    }

    // ini untuk memproses pengajuan dokumen dinas/arahan yang diunggah langsung (PDF)
    public function storeDinasArahan(\Illuminate\Http\Request $request)
    {
        if (!session()->has('pengguna')) return redirect('/');

        $request->validate([
            'id_template' => 'required|exists:template_surat,id',
            'judul' => 'required|string|max:200',
            'NOMOR_SURAT' => 'nullable|string',
            'TANGGAL_SURAT' => 'required|string',
            'TENTANG_SURAT' => 'required|string',
            'MENETAPKAN_TENTANG' => 'required|string',
            'file_lampiran' => 'nullable|array|max:10',
            'file_lampiran.*' => 'file|max:5120|mimes:pdf|mimetypes:application/pdf',
        ]);

        $template = \App\Models\TemplateSurat::findOrFail($request->id_template);
        $pengguna = session('pengguna');

        $adaLampiran = $request->hasFile('file_lampiran') ? 1 : 0;

        $pengajuan = \App\Models\Pengajuan::create([
            'id_pengguna' => $pengguna['id'],
            'id_grup_verifikasi_verifikator' => null,
            'judul' => $request->judul,
            'tipe' => $template->tipe,
            'status' => 'Diproses Admin',
            'daftar_menimbang' => '',
            'daftar_memperhatikan' => '',
            'daftar_memutuskan' => '',
            'ada_lampiran' => $adaLampiran,
            'filepath_lampiran' => null,
            'nomor_diusulkan' => $request->NOMOR_SURAT,
        ]);

        if ($request->hasFile('file_lampiran')) {
            foreach ($request->file('file_lampiran') as $file) {
                $handle = fopen($file->getRealPath(), 'rb');
                $magic  = fread($handle, 4);
                fclose($handle);
                if ($magic !== '%PDF') {
                    $pengajuan->delete();
                    return back()->with('error', 'File lampiran yang diunggah bukan file PDF yang valid.');
                }
                $namaAsli       = preg_replace('/[^\w\-\.\s]/', '', $file->getClientOriginalName());
                $filenameSimpan = 'lmp_' . $pengajuan->id . '_' . \Illuminate\Support\Str::uuid() . '.pdf';
                $file->storeAs('lampiran_pengajuan', $filenameSimpan, 'public');
                \Illuminate\Support\Facades\DB::table('lampiran_pengajuan')->insert([
                    'id_pengajuan' => $pengajuan->id,
                    'nama_file'   => $namaAsli,
                    'filepath'    => 'lampiran_pengajuan/' . $filenameSimpan,
                    'created_at'  => now(),
                    'updated_at'  => now()
                ]);
            }
        }

        $templatePath = storage_path('app/public/' . $template->filepath);
        if (!file_exists($templatePath)) {
            $pengajuan->delete();
            return back()->with('error', 'File template surat tidak ditemukan di server.');
        }

        try {
            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);
            
            $templateProcessor->setValue('NOMOR_SURAT', htmlspecialchars(trim($request->NOMOR_SURAT ?? '')) ?: '${NOMOR_SURAT}');
            $templateProcessor->setValue('TANGGAL_SURAT', \Carbon\Carbon::parse($request->TANGGAL_SURAT)->translatedFormat('d F Y'));
            $templateProcessor->setValue('TAHUN_SURAT', \Carbon\Carbon::parse($request->TANGGAL_SURAT)->format('Y'));
            $templateProcessor->setValue('TENTANG_SURAT', htmlspecialchars(trim($request->TENTANG_SURAT ?? '')) ?: '');
            $templateProcessor->setValue('MENETAPKAN_TENTANG', htmlspecialchars(trim($request->MENETAPKAN_TENTANG ?? '')) ?: '');
            
            $menimbangText = '';
            if ($request->has('menimbang') && is_array($request->menimbang)) {
                $alphabet = range('a', 'z');
                $idx = 0;
                foreach ($request->menimbang as $m) {
                    if (!empty(trim($m))) {
                        $letter = $alphabet[$idx] ?? 'a';
                        $menimbangText .= $letter . '. ' . htmlspecialchars(trim($m)) . '<w:br/>';
                        $idx++;
                    }
                }
            }
            if ($menimbangText) {
                $menimbangText = substr($menimbangText, 0, -7); // remove last <w:br/> (7 chars)
            }
            $templateProcessor->setValue('MENIMBANG_SURAT', $menimbangText ?: '${MENIMBANG_SURAT}');
            $pasalText = '';
            if ($request->has('pasal') && is_array($request->pasal)) {
                $pasalCount = 1;
                foreach ($request->pasal as $p) {
                    if (!empty(trim($p))) {
                        $pasalText .= 'Pasal ' . $pasalCount . '<w:br/>' . htmlspecialchars(trim($p)) . '<w:br/><w:br/>';
                        $pasalCount++;
                    }
                }
            }
            if ($pasalText) {
                $pasalText = substr($pasalText, 0, -14); // remove last <w:br/><w:br/> (14 chars)
            }
            $templateProcessor->setValue('PASAL_SURAT', $pasalText ?: '${PASAL_SURAT}');

            $mengingatText = '';
            if ($request->has('peraturan')) {
                $peraturans = \App\Models\Peraturan::whereIn('id', $request->peraturan)->get();
                $count = 1;
                foreach ($peraturans as $p) {
                    \Illuminate\Support\Facades\DB::table('pengajuan_peraturan')->insert([
                        'id_pengajuan' => $pengajuan->id,
                        'id_peraturan' => $p->id,
                        'created_at' => now(),
                    ]);
                    $mengingatText .= $count . '. ' . htmlspecialchars($p->kode . ' tentang ' . $p->judul) . '<w:br/>';
                    $count++;
                }
            }
            if ($mengingatText) {
                $mengingatText = substr($mengingatText, 0, -8); 
            }
            $templateProcessor->setValue('MENGINGAT', $mengingatText ?: '${MENGINGAT}');
            
            $filename = 'pengajuan_' . $pengajuan->id . '_' . time() . '.docx';
            $saveDir = storage_path('app/public/pengajuan');
            if (!file_exists($saveDir)) {
                mkdir($saveDir, 0755, true);
            }

            $templateProcessor->saveAs($saveDir . '/' . $filename);

            $pengajuan->update([
                'filepath' => 'pengajuan/' . $filename
            ]);

            \App\Models\RiwayatPengajuan::create([
                'id_pengajuan' => $pengajuan->id,
                'id_pengguna' => $pengguna['id'],
                'aksi' => 'DIBUAT',
                'catatan_aksi' => 'Pengajuan Dinas Arahan dibuat',
                'versi' => 1,
                'created_at' => now()
            ]);

            return redirect('/pengajuan/' . $pengajuan->id . '/edit')->with('success', 'Pengajuan berhasil dibuat! Silakan tinjau draf dokumen Anda.');

        } catch (\Exception $e) {
            $pengajuan->delete();
            \Illuminate\Support\Facades\Log::error('Gagal memproses template arahan #' . ($pengajuan->id ?? '?') . ': ' . $e->getMessage());
            return back()->with('error', 'Gagal memproses template surat. Silakan coba lagi atau hubungi administrator.');
        }
    }

    // form dinas instruksi
    public function formDinasInstruksi()
    {
        if (!session()->has('pengguna')) return redirect('/');
        
        $template = \App\Models\TemplateSurat::where('filepath', 'LIKE', '%dinas_instruksi%')->first();
        if (!$template) {
            return redirect('/pengajuan/baru')->with('error', 'Template Surat Dinas Arahan (Instruksi) tidak ditemukan.');
        }

        $penggunas = \App\Models\Pengguna::select('id', 'nama', 'nip', 'jabatan')->where('is_deleted', 0)->orderBy('nama')->get();

        return view('forms.dinas-instruksi', compact('template', 'penggunas'));
    }

    public function storeDinasInstruksi(\Illuminate\Http\Request $request)
    {
        if (!session()->has('pengguna')) return redirect('/');

        $request->validate([
            'id_template' => 'required|exists:template_surat,id',
            'judul' => 'required|string|max:200',
            'NOMOR_SURAT' => 'nullable|string',
            'TANGGAL_SURAT' => 'required|string',
            'JUDUL_SURAT' => 'required|string',
            'RANGKA_SURAT' => 'required|string',
            'anggota' => 'required|array|min:1',
            'anggota.*.id_pengguna' => 'required|exists:pengguna,id',
            'instruksi' => 'required|array|min:1',
            'file_lampiran' => 'nullable|array|max:10',
            'file_lampiran.*' => 'file|max:5120|mimes:pdf|mimetypes:application/pdf',
        ]);

        $template = \App\Models\TemplateSurat::findOrFail($request->id_template);
        $pengguna = session('pengguna');

        $adaLampiran = $request->hasFile('file_lampiran') ? 1 : 0;

        $pengajuan = \App\Models\Pengajuan::create([
            'id_pengguna' => $pengguna['id'],
            'id_grup_verifikasi_verifikator' => null,
            'judul' => $request->judul,
            'tipe' => $template->tipe,
            'status' => 'Diproses Admin',
            'daftar_menimbang' => '',
            'daftar_memperhatikan' => '',
            'daftar_memutuskan' => '',
            'ada_lampiran' => $adaLampiran,
            'filepath_lampiran' => null,
            'nomor_diusulkan' => $request->NOMOR_SURAT,
        ]);

        if ($request->hasFile('file_lampiran')) {
            foreach ($request->file('file_lampiran') as $file) {
                $handle = fopen($file->getRealPath(), 'rb');
                $magic  = fread($handle, 4);
                fclose($handle);
                if ($magic !== '%PDF') {
                    $pengajuan->delete();
                    return back()->with('error', 'File lampiran bukan PDF valid.');
                }
                $namaAsli       = preg_replace('/[^\w\-\.\s]/', '', $file->getClientOriginalName());
                $filenameSimpan = 'lmp_' . $pengajuan->id . '_' . \Illuminate\Support\Str::uuid() . '.pdf';
                $file->storeAs('lampiran_pengajuan', $filenameSimpan, 'public');
                \Illuminate\Support\Facades\DB::table('lampiran_pengajuan')->insert([
                    'id_pengajuan' => $pengajuan->id,
                    'nama_file'   => $namaAsli,
                    'filepath'    => 'lampiran_pengajuan/' . $filenameSimpan,
                    'created_at'  => now(),
                    'updated_at'  => now()
                ]);
            }
        }

        $templatePath = storage_path('app/public/' . $template->filepath);
        if (!file_exists($templatePath)) {
            $pengajuan->delete();
            return back()->with('error', 'File template surat tidak ditemukan di server.');
        }

        try {
            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);
            
            $templateProcessor->setValue('NOMOR_SURAT', htmlspecialchars(trim($request->NOMOR_SURAT ?? '')) ?: '${NOMOR_SURAT}');
            $templateProcessor->setValue('TANGGAL_SURAT', \Carbon\Carbon::parse($request->TANGGAL_SURAT)->translatedFormat('d F Y'));
            $templateProcessor->setValue('JUDUL_SURAT', htmlspecialchars(trim($request->JUDUL_SURAT ?? '')) ?: '');
            $templateProcessor->setValue('RANGKA_SURAT', htmlspecialchars(trim($request->RANGKA_SURAT ?? '')) ?: '');
            
            // Proses Anggota (Tabel Dinamis)
            if ($request->has('anggota') && is_array($request->anggota)) {
                $anggotaCount = count($request->anggota);
                $templateProcessor->cloneRow('ANGGOTA_SURAT', $anggotaCount);
                $i = 1;
                foreach ($request->anggota as $agt) {
                    $p = \App\Models\Pengguna::find($agt['id_pengguna']);
                    $templateProcessor->setValue('NO_ANGGOTA#' . $i, $i);
                    $namaText = $p ? $p->nama : '';
                    if ($p && $p->jabatan) {
                        $namaText .= ' (' . $p->jabatan . ')';
                    }
                    $templateProcessor->setValue('ANGGOTA_SURAT#' . $i, htmlspecialchars($namaText));
                    $i++;
                }
            } else {
                $templateProcessor->cloneRow('ANGGOTA_SURAT', 1);
                $templateProcessor->setValue('NO_ANGGOTA#1', '1');
                $templateProcessor->setValue('ANGGOTA_SURAT#1', '');
            }

            // Proses Instruksi (Tabel Dinamis)
            $urutanKata = ["KESATU", "KEDUA", "KETIGA", "KEEMPAT", "KELIMA", "KEENAM", "KETUJUH", "KEDELAPAN", "KESEMBILAN", "KESEPULUH"];
            if ($request->has('instruksi') && is_array($request->instruksi)) {
                // Filter out empty instructions
                $instruksiValid = array_filter($request->instruksi, function($v) { return !empty(trim($v)); });
                $instruksiValid = array_values($instruksiValid); // reindex
                
                $instruksiCount = count($instruksiValid);
                if ($instruksiCount > 0) {
                    $templateProcessor->cloneRow('ISI_INSTRUKSI', $instruksiCount);
                    $i = 1;
                    foreach ($instruksiValid as $ins) {
                        $label = isset($urutanKata[$i - 1]) ? $urutanKata[$i - 1] : ("KE-" . $i);
                        $templateProcessor->setValue('LABEL_INSTRUKSI#' . $i, $label);
                        $templateProcessor->setValue('ISI_INSTRUKSI#' . $i, htmlspecialchars(trim($ins)));
                        $i++;
                    }
                } else {
                    $templateProcessor->cloneRow('ISI_INSTRUKSI', 1);
                    $templateProcessor->setValue('LABEL_INSTRUKSI#1', 'KESATU');
                    $templateProcessor->setValue('ISI_INSTRUKSI#1', '');
                }
            } else {
                $templateProcessor->cloneRow('ISI_INSTRUKSI', 1);
                $templateProcessor->setValue('LABEL_INSTRUKSI#1', 'KESATU');
                $templateProcessor->setValue('ISI_INSTRUKSI#1', '');
            }
            
            // Dummy QR dan Verifikator
            $templateProcessor->setValue('QR_CODE', '${QR_CODE}');
            $templateProcessor->setValue('NAMA_VERIFIKATOR', '${NAMA_VERIFIKATOR}');

            $filename = 'pengajuan_' . $pengajuan->id . '_' . time() . '.docx';
            $saveDir = storage_path('app/public/pengajuan');
            if (!file_exists($saveDir)) {
                mkdir($saveDir, 0755, true);
            }

            $templateProcessor->saveAs($saveDir . '/' . $filename);

            $pengajuan->update([
                'filepath' => 'pengajuan/' . $filename
            ]);

            \App\Models\RiwayatPengajuan::create([
                'id_pengajuan' => $pengajuan->id,
                'id_pengguna' => $pengguna['id'],
                'aksi' => 'DIBUAT',
                'catatan_aksi' => 'Pengajuan Dinas Instruksi dibuat',
                'versi' => 1,
                'created_at' => now()
            ]);

            return redirect('/pengajuan/' . $pengajuan->id . '/edit')->with('success', 'Pengajuan berhasil dibuat! Silakan tinjau draf dokumen Anda.');

        } catch (\Exception $e) {
            $pengajuan->delete();
            \Illuminate\Support\Facades\Log::error('Gagal memproses template instruksi #' . ($pengajuan->id ?? '?') . ': ' . $e->getMessage());
            return back()->with('error', 'Gagal memproses template surat. Silakan coba lagi. ' . $e->getMessage());
        }
    }

    // ini untuk membuka editor OnlyOffice (view 'pengajuan-edit') untuk mengedit dokumen pengajuan

    // =========================================================================
    // NASKAH DINAS KORESPONDENSI
    // =========================================================================

    public function formKorespondensiNota()
    {
        if (!session()->has('pengguna')) return redirect('/');
        
        $template = \App\Models\TemplateSurat::where('filepath', 'LIKE', '%korespondensi_nota_dinas%')->first();
        if (!$template) {
            return redirect('/pengajuan/baru')->with('error', 'Template Nota Dinas tidak ditemukan.');
        }

        return view('forms.korespondensi-nota', compact('template'));
    }

    public function storeKorespondensiNota(\Illuminate\Http\Request $request)
    {
        if (!session()->has('pengguna')) return redirect('/');
        $pengguna = session('pengguna');
        
        $request->validate([
            'id_template' => 'required|exists:template_surat,id',
            'judul' => 'required|string|max:200',
            'file_lampiran' => 'nullable|array|max:10',
            'file_lampiran.*' => 'file|max:5120|mimes:pdf|mimetypes:application/pdf',
        ]);

        $template = \App\Models\TemplateSurat::findOrFail($request->id_template);
        $adaLampiran = $request->hasFile('file_lampiran') ? 1 : 0;

        $pengajuan = \App\Models\Pengajuan::create([
            'id_pengguna' => $pengguna['id'],
            'id_grup_verifikasi_verifikator' => null,
            'judul' => $request->judul,
            'tipe' => $template->tipe,
            'status' => 'Diproses Admin',
            'daftar_menimbang' => '',
            'daftar_memperhatikan' => '',
            'daftar_memutuskan' => '',
            'ada_lampiran' => $adaLampiran,
            'filepath_lampiran' => null,
            'nomor_diusulkan' => null,
        ]);

        $this->handleLampiran($request, $pengajuan);

        $templatePath = storage_path('app/public/' . $template->filepath);
        if (file_exists($templatePath)) {
            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);
            
            $templateProcessor->setValue('PENERIMA_SURAT', $request->penerima_surat ?? '-');
            $templateProcessor->setValue('NAMA_PENGUSUL', $request->nama_pengusul ?? '-');
            $templateProcessor->setValue('HAL', $request->hal ?? '-');
            
            // Format textareas to preserve line breaks
            $templateProcessor->setValue('KALIMAT_PEMBUKA', str_replace("\n", '<w:br/>', htmlspecialchars($request->kalimat_pembuka ?? '-')));
            $templateProcessor->setValue('ISI_SURAT', str_replace("\n", '<w:br/>', htmlspecialchars($request->isi_surat ?? '-')));
            $templateProcessor->setValue('KALIMAT_PENUTUP', str_replace("\n", '<w:br/>', htmlspecialchars($request->kalimat_penutup ?? '-')));

            $filename = 'Draft_Nota_Dinas_' . time() . '.docx';
            $templateProcessor->saveAs(storage_path('app/public/pengajuan/' . $filename));
            
            $pengajuan->update(['filepath' => 'pengajuan/' . $filename]);
            \App\Models\RiwayatPengajuan::create(['id_pengajuan' => $pengajuan->id, 'id_pengguna' => $pengguna['id'], 'aksi' => 'DIBUAT', 'catatan_aksi' => 'Pengajuan baru dibuat', 'versi' => 1, 'created_at' => now()]);
        }

        return redirect('/pengajuan/' . $pengajuan->id . '/edit')->with('success', 'Pengajuan berhasil dibuat! Silakan tinjau draf dokumen Anda.');
    }

    public function formKorespondensiDinas()
    {
        if (!session()->has('pengguna')) return redirect('/');
        
        $template = \App\Models\TemplateSurat::where('filepath', 'LIKE', '%korespondensi_surat_dinas%')->first();
        if (!$template) {
            return redirect('/pengajuan/baru')->with('error', 'Template Surat Dinas tidak ditemukan.');
        }

        return view('forms.korespondensi-dinas', compact('template'));
    }

    public function storeKorespondensiDinas(\Illuminate\Http\Request $request)
    {
        if (!session()->has('pengguna')) return redirect('/');
        $pengguna = session('pengguna');
        
        $request->validate([
            'id_template' => 'required|exists:template_surat,id',
            'judul' => 'required|string|max:200',
            'file_lampiran' => 'nullable|array|max:10',
            'file_lampiran.*' => 'file|max:5120|mimes:pdf|mimetypes:application/pdf',
        ]);

        $template = \App\Models\TemplateSurat::findOrFail($request->id_template);
        $adaLampiran = $request->hasFile('file_lampiran') ? 1 : 0;

        $pengajuan = \App\Models\Pengajuan::create([
            'id_pengguna' => $pengguna['id'],
            'id_grup_verifikasi_verifikator' => null,
            'judul' => $request->judul,
            'tipe' => $template->tipe,
            'status' => 'Diproses Admin',
            'daftar_menimbang' => '',
            'daftar_memperhatikan' => '',
            'daftar_memutuskan' => '',
            'ada_lampiran' => $adaLampiran,
            'filepath_lampiran' => null,
            'nomor_diusulkan' => null,
        ]);

        $this->handleLampiran($request, $pengajuan);

        $templatePath = storage_path('app/public/' . $template->filepath);
        if (file_exists($templatePath)) {
            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);
            
            $jumlahLampiranInt = $request->hasFile('file_lampiran') ? count($request->file('file_lampiran')) : 0;
            $teksLampiran = '-';
            if ($jumlahLampiranInt > 0) {
                $formatter = new \NumberFormatter('id', \NumberFormatter::SPELLOUT);
                $terbilang = ucfirst($formatter->format($jumlahLampiranInt));
                $teksLampiran = $jumlahLampiranInt . ' (' . $terbilang . ') Berkas';
            }
            $templateProcessor->setValue('JUMLAH-LAMPIRAN', $teksLampiran);
            $templateProcessor->setValue('HAL', $request->hal ?? '-');
            
            // Format textareas to preserve line breaks
            $templateProcessor->setValue('PENERIMA_SURAT', str_replace("\n", '<w:br/>', htmlspecialchars($request->penerima_surat ?? '-')));
            $templateProcessor->setValue('KALIMAT_PEMBUKA', str_replace("\n", '<w:br/>', htmlspecialchars($request->kalimat_pembuka ?? '-')));
            $templateProcessor->setValue('ISI_SURAT', str_replace("\n", '<w:br/>', htmlspecialchars($request->isi_surat ?? '-')));
            $templateProcessor->setValue('KALIMAT_PENUTUP', str_replace("\n", '<w:br/>', htmlspecialchars($request->kalimat_penutup ?? '-')));

            $filename = 'Draft_Surat_Dinas_' . time() . '.docx';
            $templateProcessor->saveAs(storage_path('app/public/pengajuan/' . $filename));
            
            $pengajuan->update(['filepath' => 'pengajuan/' . $filename]);
            \App\Models\RiwayatPengajuan::create(['id_pengajuan' => $pengajuan->id, 'id_pengguna' => $pengguna['id'], 'aksi' => 'DIBUAT', 'catatan_aksi' => 'Pengajuan baru dibuat', 'versi' => 1, 'created_at' => now()]);
        }

        return redirect('/pengajuan/' . $pengajuan->id . '/edit')->with('success', 'Pengajuan berhasil dibuat! Silakan tinjau draf dokumen Anda.');
    }

    public function formKorespondensiUndangan()
    {
        if (!session()->has('pengguna')) return redirect('/');
        
        $template = \App\Models\TemplateSurat::where('filepath', 'LIKE', '%korespondensi_surat_undangan%')->first();
        if (!$template) {
            return redirect('/pengajuan/baru')->with('error', 'Template Surat Undangan tidak ditemukan.');
        }

        return view('forms.korespondensi-undangan', compact('template'));
    }

    public function storeKorespondensiUndangan(\Illuminate\Http\Request $request)
    {
        if (!session()->has('pengguna')) return redirect('/');
        $pengguna = session('pengguna');
        
        $request->validate([
            'id_template' => 'required|exists:template_surat,id',
            'judul' => 'required|string|max:200',
            'file_lampiran' => 'nullable|array|max:10',
            'file_lampiran.*' => 'file|max:5120|mimes:pdf|mimetypes:application/pdf',
        ]);

        $template = \App\Models\TemplateSurat::findOrFail($request->id_template);
        $adaLampiran = $request->hasFile('file_lampiran') ? 1 : 0;

        $pengajuan = \App\Models\Pengajuan::create([
            'id_pengguna' => $pengguna['id'],
            'id_grup_verifikasi_verifikator' => null,
            'judul' => $request->judul,
            'tipe' => $template->tipe,
            'status' => 'Diproses Admin',
            'daftar_menimbang' => '',
            'daftar_memperhatikan' => '',
            'daftar_memutuskan' => '',
            'ada_lampiran' => $adaLampiran,
            'filepath_lampiran' => null,
            'nomor_diusulkan' => null,
        ]);

        $this->handleLampiran($request, $pengajuan);

        $templatePath = storage_path('app/public/' . $template->filepath);
        if (file_exists($templatePath)) {
            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);
            
            $jumlahLampiranInt = $request->hasFile('file_lampiran') ? count($request->file('file_lampiran')) : 0;
            $teksLampiran = '-';
            if ($jumlahLampiranInt > 0) {
                $formatter = new \NumberFormatter('id', \NumberFormatter::SPELLOUT);
                $terbilang = ucfirst($formatter->format($jumlahLampiranInt));
                $teksLampiran = $jumlahLampiranInt . ' (' . $terbilang . ') Berkas';
            }
            $templateProcessor->setValue('JUMLAH-LAMPIRAN', $teksLampiran);
            $templateProcessor->setValue('HAL', $request->hal ?? '-');
            $templateProcessor->setValue('HARI_DAN_TANGGAL_ACARA', $request->hari_tanggal_acara ?? '-');
            $templateProcessor->setValue('WAKTU_ACARA', $request->waktu_acara ?? '-');
            $templateProcessor->setValue('TEMPAT_ACARA', $request->tempat_acara ?? '-');
            $templateProcessor->setValue('NAMA_ACARA', $request->nama_acara ?? '-');
            
            // Format textareas to preserve line breaks
            $templateProcessor->setValue('PENERIMA_SURAT', str_replace("\n", '<w:br/>', htmlspecialchars($request->penerima_surat ?? '-')));
            $templateProcessor->setValue('KALIMAT_PEMBUKA', str_replace("\n", '<w:br/>', htmlspecialchars($request->kalimat_pembuka ?? '-')));
            $templateProcessor->setValue('KALIMAT_PENUTUP', str_replace("\n", '<w:br/>', htmlspecialchars($request->kalimat_penutup ?? '-')));

            $filename = 'Draft_Surat_Undangan_' . time() . '.docx';
            $templateProcessor->saveAs(storage_path('app/public/pengajuan/' . $filename));
            
            $pengajuan->update(['filepath' => 'pengajuan/' . $filename]);
            \App\Models\RiwayatPengajuan::create(['id_pengajuan' => $pengajuan->id, 'id_pengguna' => $pengguna['id'], 'aksi' => 'DIBUAT', 'catatan_aksi' => 'Pengajuan baru dibuat', 'versi' => 1, 'created_at' => now()]);
        }

        return redirect('/pengajuan/' . $pengajuan->id . '/edit')->with('success', 'Pengajuan berhasil dibuat! Silakan tinjau draf dokumen Anda.');
    }

    // ==========================================
    // DINAS KHUSUS - KETERANGAN
    // ==========================================
    public function formDinasKhususKeterangan(Request $request)
    {
        $template = TemplateSurat::findOrFail($request->query('id'));
        $penggunas = \App\Models\Pengguna::where('is_deleted', 0)->select('id', 'nama')->get();
        return view('forms.dinas-khusus-keterangan', compact('template', 'penggunas'));
    }

    public function storeDinasKhususKeterangan(Request $request)
    {
        $pengguna = session('pengguna');
        if (!$pengguna) return redirect('/login')->with('error', 'Silakan login terlebih dahulu.');

        $template = TemplateSurat::findOrFail($request->id_template);
        
        $pengajuan = Pengajuan::create([
            'id_pengguna' => $pengguna['id'],
            'id_template' => $template->id,
            'judul' => $request->judul,
            'status' => 'Diproses Admin',
            'tipe' => $template->tipe
        ]);

        $this->handleLampiran($request, $pengajuan);

        $outputPath = 'pengajuan/' . time() . '_' . basename($template->filepath);
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor(storage_path('app/public/' . $template->filepath));

        $templateProcessor->setValue('NAMA_PENGUSULAN', $request->NAMA_PENGUSULAN ?? '-');
        $templateProcessor->setValue('NIP_PENGUSULAN', $request->NIP_PENGUSULAN ?? '-');
        $templateProcessor->setValue('PANGKAT_PENGUSULAN', $request->PANGKAT_PENGUSULAN ?? '-');
        $templateProcessor->setValue('GOLONGAN_PENGUSULAN', $request->GOLONGAN_PENGUSULAN ?? '-');
        $templateProcessor->setValue('JABATAN_PENGUSULAN', $request->JABATAN_PENGUSULAN ?? '-');

        $templateProcessor->setValue('NAMA_DIUSULIN', $request->NAMA_DIUSULIN ?? '-');
        $templateProcessor->setValue('NIP_DIUSULIN', $request->NIP_DIUSULIN ?? '-');
        $templateProcessor->setValue('PANGKAT_DIUSULIN', $request->PANGKAT_DIUSULIN ?? '-');
        $templateProcessor->setValue('GOLONGAN_DIUSULIN', $request->GOLONGAN_DIUSULIN ?? '-');
        $templateProcessor->setValue('JABATAN_DIUSULIN', $request->JABATAN_DIUSULIN ?? '-');

        $templateProcessor->setValue('ISI_KETERANGAN', str_replace("\n", '<w:br/>', $request->ISI_KETERANGAN ?? '-'));
        $templateProcessor->setValue('TANGGAL_SURAT', now()->translatedFormat('d F Y'));

        $templateProcessor->saveAs(storage_path('app/public/' . $outputPath));

                $pengajuan->update(['filepath' => $outputPath]);

        \App\Models\RiwayatPengajuan::create([
            'id_pengajuan' => $pengajuan->id, 
            'id_pengguna' => $pengguna['id'], 
            'aksi' => 'DIBUAT', 
            'catatan_aksi' => 'Pengajuan baru dibuat', 
            'versi' => 1, 
            'created_at' => now()
        ]);

        return redirect('/pengajuan/' . $pengajuan->id . '/edit')->with('success', 'Pengajuan berhasil dibuat! Silakan tinjau draf dokumen Anda.');
    }

    // ==========================================
    // DINAS KHUSUS - PENGUMUMAN
    // ==========================================
    public function formDinasKhususPengumuman(Request $request)
    {
        $template = TemplateSurat::findOrFail($request->query('id'));
        $penggunas = \App\Models\Pengguna::where('is_deleted', 0)->select('id', 'nama')->get();
        return view('forms.dinas-khusus-pengumuman', compact('template', 'penggunas'));
    }

    public function storeDinasKhususPengumuman(Request $request)
    {
        $pengguna = session('pengguna');
        if (!$pengguna) return redirect('/login')->with('error', 'Silakan login terlebih dahulu.');

        $template = TemplateSurat::findOrFail($request->id_template);
        
        $pengajuan = Pengajuan::create([
            'id_pengguna' => $pengguna['id'],
            'id_template' => $template->id,
            'judul' => $request->judul,
            'status' => 'Diproses Admin',
            'tipe' => $template->tipe
        ]);

        $this->handleLampiran($request, $pengajuan);

        $outputPath = 'pengajuan/' . time() . '_' . basename($template->filepath);
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor(storage_path('app/public/' . $template->filepath));

        $templateProcessor->setValue('PARAGRAF_PENGUMUMAN1', str_replace("\n", '<w:br/>', $request->PARAGRAF_PENGUMUMAN1 ?? '-'));
        $templateProcessor->setValue('PARAGRAF_PENGUMUMAN2', str_replace("\n", '<w:br/>', $request->PARAGRAF_PENGUMUMAN2 ?? '-'));
        $templateProcessor->setValue('PARAGRAF_PENGUMUMAN3', str_replace("\n", '<w:br/>', $request->PARAGRAF_PENGUMUMAN3 ?? '-'));
        $templateProcessor->setValue('TANGGAL_SURAT', now()->translatedFormat('d F Y'));

        $templateProcessor->saveAs(storage_path('app/public/' . $outputPath));

                $pengajuan->update(['filepath' => $outputPath]);

        \App\Models\RiwayatPengajuan::create([
            'id_pengajuan' => $pengajuan->id, 
            'id_pengguna' => $pengguna['id'], 
            'aksi' => 'DIBUAT', 
            'catatan_aksi' => 'Pengajuan baru dibuat', 
            'versi' => 1, 
            'created_at' => now()
        ]);

        return redirect('/pengajuan/' . $pengajuan->id . '/edit')->with('success', 'Pengajuan berhasil dibuat! Silakan tinjau draf dokumen Anda.');
    }

    // ==========================================
    // DINAS KHUSUS - PERNYATAAN
    // ==========================================
    public function formDinasKhususPernyataan(Request $request)
    {
        $template = TemplateSurat::findOrFail($request->query('id'));
        $penggunas = \App\Models\Pengguna::where('is_deleted', 0)->select('id', 'nama')->get();
        return view('forms.dinas-khusus-pernyataan', compact('template', 'penggunas'));
    }

    public function storeDinasKhususPernyataan(Request $request)
    {
        $pengguna = session('pengguna');
        if (!$pengguna) return redirect('/login')->with('error', 'Silakan login terlebih dahulu.');

        $template = TemplateSurat::findOrFail($request->id_template);
        
        $pengajuan = Pengajuan::create([
            'id_pengguna' => $pengguna['id'],
            'id_template' => $template->id,
            'judul' => $request->judul,
            'status' => 'Diproses Admin',
            'tipe' => $template->tipe
        ]);

        $this->handleLampiran($request, $pengajuan);

        $outputPath = 'pengajuan/' . time() . '_' . basename($template->filepath);
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor(storage_path('app/public/' . $template->filepath));

        $templateProcessor->setValue('NAMA_PENGUSULAN', $request->NAMA_PENGUSULAN ?? '-');
        $templateProcessor->setValue('NIP_PENGUSULAN', $request->NIP_PENGUSULAN ?? '-');
        $templateProcessor->setValue('PANGKAT_PENGUSULAN', $request->PANGKAT_PENGUSULAN ?? '-');
        $templateProcessor->setValue('GOLONGAN_PENGUSULAN', $request->GOLONGAN_PENGUSULAN ?? '-');
        $templateProcessor->setValue('JABATAN_PENGUSULAN', $request->JABATAN_PENGUSULAN ?? '-');
        $templateProcessor->setValue('ALAMAT_PENGUSULAN', $request->ALAMAT_PENGUSULAN ?? '-');

        $templateProcessor->setValue('PERNYATAAN_SURAT', str_replace("\n", '<w:br/>', $request->PERNYATAAN_SURAT ?? '-'));
        $templateProcessor->setValue('TANGGAL_SURAT', now()->translatedFormat('d F Y'));

        $templateProcessor->saveAs(storage_path('app/public/' . $outputPath));

                $pengajuan->update(['filepath' => $outputPath]);

        \App\Models\RiwayatPengajuan::create([
            'id_pengajuan' => $pengajuan->id, 
            'id_pengguna' => $pengguna['id'], 
            'aksi' => 'DIBUAT', 
            'catatan_aksi' => 'Pengajuan baru dibuat', 
            'versi' => 1, 
            'created_at' => now()
        ]);

        return redirect('/pengajuan/' . $pengajuan->id . '/edit')->with('success', 'Pengajuan berhasil dibuat! Silakan tinjau draf dokumen Anda.');
    }

    // ==========================================
    // DINAS KHUSUS - REKOMENDASI
    // ==========================================
    public function formDinasKhususRekomendasi(Request $request)
    {
        $template = TemplateSurat::findOrFail($request->query('id'));
        $penggunas = \App\Models\Pengguna::where('is_deleted', 0)->select('id', 'nama')->get();
        return view('forms.dinas-khusus-rekomendasi', compact('template', 'penggunas'));
    }

    public function storeDinasKhususRekomendasi(Request $request)
    {
        $pengguna = session('pengguna');
        if (!$pengguna) return redirect('/login')->with('error', 'Silakan login terlebih dahulu.');

        $template = TemplateSurat::findOrFail($request->id_template);
        
        $pengajuan = Pengajuan::create([
            'id_pengguna' => $pengguna['id'],
            'id_template' => $template->id,
            'judul' => $request->judul,
            'status' => 'Diproses Admin',
            'tipe' => $template->tipe
        ]);

        $this->handleLampiran($request, $pengajuan);

        $outputPath = 'pengajuan/' . time() . '_' . basename($template->filepath);
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor(storage_path('app/public/' . $template->filepath));

        $templateProcessor->setValue('NAMA_PENGUSULAN', $request->NAMA_PENGUSULAN ?? '-');
        $templateProcessor->setValue('NIP_PENGUSULAN', $request->NIP_PENGUSULAN ?? '-');
        $templateProcessor->setValue('PANGKAT_PENGUSULAN', $request->PANGKAT_PENGUSULAN ?? '-');
        $templateProcessor->setValue('GOLONGAN_PENGUSULAN', $request->GOLONGAN_PENGUSULAN ?? '-');
        $templateProcessor->setValue('JABATAN_PENGUSULAN', $request->JABATAN_PENGUSULAN ?? '-');
        $templateProcessor->setValue('ALAMAT_PENGUSULAN', $request->ALAMAT_PENGUSULAN ?? '-');

        $templateProcessor->setValue('NAMA_DIUSULIN', $request->NAMA_DIUSULIN ?? '-');
        $templateProcessor->setValue('NIP_DIUSULIN', $request->NIP_DIUSULIN ?? '-');
        $templateProcessor->setValue('PANGKAT_DIUSULIN', $request->PANGKAT_DIUSULIN ?? '-');
        $templateProcessor->setValue('GOLONGAN_DIUSULIN', $request->GOLONGAN_DIUSULIN ?? '-');
        $templateProcessor->setValue('JABATAN_DIUSULIN', $request->JABATAN_DIUSULIN ?? '-');

        $templateProcessor->setValue('REKOMENDASI_UNTUK_APA', str_replace("\n", '<w:br/>', $request->REKOMENDASI_UNTUK_APA ?? '-'));
        $templateProcessor->setValue('TANGGAL_SURAT', now()->translatedFormat('d F Y'));

        $templateProcessor->saveAs(storage_path('app/public/' . $outputPath));

                $pengajuan->update(['filepath' => $outputPath]);

        \App\Models\RiwayatPengajuan::create([
            'id_pengajuan' => $pengajuan->id, 
            'id_pengguna' => $pengguna['id'], 
            'aksi' => 'DIBUAT', 
            'catatan_aksi' => 'Pengajuan baru dibuat', 
            'versi' => 1, 
            'created_at' => now()
        ]);

        return redirect('/pengajuan/' . $pengajuan->id . '/edit')->with('success', 'Pengajuan berhasil dibuat! Silakan tinjau draf dokumen Anda.');
    }

    // ==========================================
    // DINAS KHUSUS - PENGANTAR (Dengan Tabel Dinamis)
    // ==========================================
    public function formDinasKhususPengantar(Request $request)
    {
        $template = TemplateSurat::findOrFail($request->query('id'));
        $penggunas = \App\Models\Pengguna::where('is_deleted', 0)->select('id', 'nama')->get();
        return view('forms.dinas-khusus-pengantar', compact('template', 'penggunas'));
    }

    public function storeDinasKhususPengantar(Request $request)
    {
        $pengguna = session('pengguna');
        if (!$pengguna) return redirect('/login')->with('error', 'Silakan login terlebih dahulu.');

        $template = TemplateSurat::findOrFail($request->id_template);
        
        $pengajuan = Pengajuan::create([
            'id_pengguna' => $pengguna['id'],
            'id_template' => $template->id,
            'judul' => $request->judul,
            'status' => 'Diproses Admin',
            'tipe' => $template->tipe
        ]);

        $this->handleLampiran($request, $pengajuan);

        $outputPath = 'pengajuan/' . time() . '_' . basename($template->filepath);
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor(storage_path('app/public/' . $template->filepath));

        $templateProcessor->setValue('NAMA_PENERIMA_SURAT', $request->NAMA_PENERIMA_SURAT ?? '-');
        $templateProcessor->setValue('KALIMAT_PEMBUKA', $request->KALIMAT_PEMBUKA ?? '-');
        $templateProcessor->setValue('KALIMAT_PENUTUP', $request->KALIMAT_PENUTUP ?? '-');
        $templateProcessor->setValue('TANGGAL_SURAT', now()->translatedFormat('d F Y'));

        // Handle Tabel Dinamis (dokumen array)
        $dokumenInput = $request->input('dokumen');
        if (!empty($dokumenInput) && is_array($dokumenInput)) {
            $values = [];
            $no = 1;
            foreach ($dokumenInput as $dok) {
                $values[] = [
                    'NO' => $no++,
                    'JENIS_DOKUMEN' => $dok['jenis'] ?? '-',
                    'JUMLAH' => $dok['jumlah'] ?? '-',
                    'KETERANGAN' => $dok['keterangan'] ?? '-'
                ];
            }
            $templateProcessor->cloneRowAndSetValues('NO', $values);
        } else {
            // Jika kosong (walau harusnya required di form), clone 1 row kosong
            $templateProcessor->cloneRowAndSetValues('NO', [
                ['NO' => '-', 'JENIS_DOKUMEN' => '-', 'JUMLAH' => '-', 'KETERANGAN' => '-']
            ]);
        }

        $templateProcessor->saveAs(storage_path('app/public/' . $outputPath));

                $pengajuan->update(['filepath' => $outputPath]);

        \App\Models\RiwayatPengajuan::create([
            'id_pengajuan' => $pengajuan->id, 
            'id_pengguna' => $pengguna['id'], 
            'aksi' => 'DIBUAT', 
            'catatan_aksi' => 'Pengajuan baru dibuat', 
            'versi' => 1, 
            'created_at' => now()
        ]);

        return redirect('/pengajuan/' . $pengajuan->id . '/edit')->with('success', 'Pengajuan berhasil dibuat! Silakan tinjau draf dokumen Anda.');
    }
}