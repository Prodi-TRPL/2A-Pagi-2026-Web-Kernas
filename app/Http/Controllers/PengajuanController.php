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
        // Mengambil semua grup verifikasi (Dosen akan memilih rute verifikasi mana)
        $grupVerifikasi = GrupVerifikasi::all();
        
        return view('pengajuan-baru', compact('templates', 'grupVerifikasi'));
    }

    public function store(Request $request)
    {
        if (!session()->has('pengguna')) return redirect('/');

        $request->validate([
            'id_template' => 'required|exists:template_surat,id',
            'judul' => 'required|string|max:200',
            'daftar_menimbang' => 'required|string',
            'daftar_memperhatikan' => 'required|string',
            'daftar_memutuskan' => 'required|string',
            'id_grup_verifikasi' => 'required|exists:grup_verifikasi,id'
        ]);

        $template = TemplateSurat::findOrFail($request->id_template);
        $pengguna = session('pengguna');

        // Create initial pengajuan record
        $pengajuan = Pengajuan::create([
            'id_pengguna' => $pengguna['id'],
            'id_grup_verifikasi_verifikator' => $request->id_grup_verifikasi,
            'judul' => $request->judul,
            'tipe' => $template->tipe,
            'status' => 'POSTED',
            'daftar_menimbang' => $request->daftar_menimbang,
            'daftar_memperhatikan' => $request->daftar_memperhatikan,
            'daftar_memutuskan' => $request->daftar_memutuskan,
            'ada_lampiran' => 0
        ]);

        // Process Word Template
        $templatePath = storage_path('app/public/' . $template->filepath);
        if (!file_exists($templatePath)) {
            return back()->with('error', 'File template surat tidak ditemukan di server.');
        }

        try {
            $templateProcessor = new TemplateProcessor($templatePath);
            
            // Replace placeholders
            // Karena ini untuk Word, karakter newline (\n) harus diubah jadi <w:br/>
            $templateProcessor->setValue('judul', $request->judul);
            $templateProcessor->setValue('menimbang', str_replace("\n", '<w:br/>', htmlspecialchars($request->daftar_menimbang)));
            $templateProcessor->setValue('memperhatikan', str_replace("\n", '<w:br/>', htmlspecialchars($request->daftar_memperhatikan)));
            $templateProcessor->setValue('memutuskan', str_replace("\n", '<w:br/>', htmlspecialchars($request->daftar_memutuskan)));
            
            // Ganti nama instansi/unit pembuat, dst sesuai kebutuhan
            $templateProcessor->setValue('nama_pembuat', $pengguna['nama']);

            // Generate new filename
            $filename = 'pengajuan_' . $pengajuan->id . '_' . time() . '.docx';
            $saveDir = storage_path('app/public/pengajuan');
            if (!file_exists($saveDir)) {
                mkdir($saveDir, 0755, true);
            }

            $templateProcessor->saveAs($saveDir . '/' . $filename);

            // Update pengajuan with new filepath
            $pengajuan->update([
                'filepath' => 'pengajuan/' . $filename
            ]);

            return redirect('/pengajuan/' . $pengajuan->id . '/edit')->with('success', 'Pengajuan berhasil dibuat! Silakan periksa draf kasar dokumen Anda di editor berikut.');

        } catch (\Exception $e) {
            // Hapus row jika pemrosesan word gagal
            $pengajuan->delete();
            return back()->with('error', 'Gagal memproses template: ' . $e->getMessage());
        }
    }

    public function editDoc($id)
    {
        if (!session()->has('pengguna')) return redirect('/');
        
        $pengajuan = Pengajuan::findOrFail($id);

        // Hanya pembuat yang bisa mengedit di status POSTED, atau Admin, dll
        // (Kita skip security complex sementara untuk keperluan demo)

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
                    "edit" => $pengajuan->status == 'POSTED' // Hanya bisa diedit jika POSTED
                ]
            ],
            "documentType" => "word",
            "editorConfig" => [
                "mode" => $pengajuan->status == 'POSTED' ? "edit" : "view",
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

        return view('pengajuan-edit', compact('pengajuan', 'config'));
    }
}
