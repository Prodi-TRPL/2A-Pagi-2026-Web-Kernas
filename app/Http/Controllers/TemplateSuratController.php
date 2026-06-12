<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TemplateSurat;
use App\Models\Masukan;

class TemplateSuratController extends Controller
{
    public function index()
    {
        if (!session()->has('pengguna')) return redirect('/');

        $templates = TemplateSurat::with('masukan', 'pembuat')
            ->orderBy('tipe')
            ->orderBy('nama_template')
            ->get()
            ->map(function ($t) {
                // Konfigurasi ONLYOFFICE
                $url = asset('storage/' . $t->filepath);
                // Ganti localhost atau 127.0.0.1 menjadi host.docker.internal agar container bisa mendownload
                $url = str_replace(['localhost', '127.0.0.1'], 'host.docker.internal', $url);
                
                $callbackUrl = asset('onlyoffice/callback/' . $t->id);
                $callbackUrl = str_replace(['localhost', '127.0.0.1'], 'host.docker.internal', $callbackUrl);

                $config = [
                    "document" => [
                        "fileType" => "docx",
                        "key" => "tmpl_" . $t->id . "_" . strtotime($t->updated_at ?? now()),
                        "title" => $t->nama_template . ".docx",
                        "url" => $url
                    ],
                    "documentType" => "word",
                    "editorConfig" => [
                        "mode" => "edit",
                        "lang" => "id-ID",
                        "user" => [
                            "id" => (string) session('pengguna.id', '1'),
                            "name" => session('pengguna.nama', 'Admin')
                        ],
                        "customization" => [
                            "autosave" => true,
                            "forcesave" => true,
                        ],
                        "callbackUrl" => $callbackUrl,
                    ]
                ];
                
                $config["width"] = "100%";
                $config["height"] = "100%";

                // Jika library JWT tersedia (di-install via Composer), hasilkan token
                // Kunci JWT wajib minimal 32 karakter (256 bit) untuk firebase/php-jwt versi 7+
                $secret = env('ONLYOFFICE_JWT_SECRET', 'polibatam_secret_jwt_key_256bit_2026');
                if (class_exists(\Firebase\JWT\JWT::class)) {
                    $token = \Firebase\JWT\JWT::encode($config, $secret, 'HS256');
                    $config['token'] = $token;
                }

                return [
                    'id' => $t->id,
                    'jenis' => $t->tipe,
                    'nama' => $t->nama_template,
                    'versi' => $t->versi,
                    'is_aktif' => $t->is_aktif == 1,
                    'filepath' => $url,
                    'raw_filepath' => $t->filepath,
                    'onlyoffice_config' => $config,
                    'konten' => '<p>Document loaded via ONLYOFFICE</p>',
                    'dibuat_oleh' => $t->pembuat ? $t->pembuat->nama : 'Sistem',
                    'tgl_dibuat' => $t->created_at ? $t->created_at->format('d M Y') : 'Baru saja',
                    'updated_at' => $t->updated_at ? $t->updated_at->format('Y-m-d') : null
                ];
            });

        return view('template-surat', compact('templates'));
    }
}
