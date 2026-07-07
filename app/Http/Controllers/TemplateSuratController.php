<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TemplateSurat;

class TemplateSuratController extends Controller
{
    // ini untuk mengambil daftar template surat beserta konfigurasi OnlyOffice-nya lalu menampilkannya di view 'template-surat'
    public function index()
    {
        if (!session()->has('pengguna')) return redirect('/');

        $templates = TemplateSurat::with('pembuat')
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

                // library JWT (di-install via Composer), hasilkan token
                // Kunci JWT wajib minimal 32 karakter
                $secret = env('ONLYOFFICE_JWT_SECRET');
                if (!empty($secret) && class_exists(\Firebase\JWT\JWT::class)) {
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
    // ini untuk memperbarui informasi dasar template surat (nama dan tipe) di database
    public function update(Request $request, $id)
    {
        if (!session()->has('pengguna')) return response()->json(['message' => 'Unauthorized'], 401);
        
        $pengguna = session('pengguna');
        if (!$pengguna['is_admin']) return response()->json(['message' => 'Forbidden'], 403);

        $request->validate([
            'nama_template' => 'required|string|max:255',
            'tipe' => 'required|string|max:100',
        ]);

        $template = TemplateSurat::findOrFail($id);
        $template->update([
            'nama_template' => $request->nama_template,
            'tipe' => $request->tipe
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $template->id,
                'jenis' => $template->tipe,
                'nama' => $template->nama_template,
                'versi' => $template->versi,
                'is_aktif' => $template->is_aktif == 1,
                'filepath' => asset('storage/' . $template->filepath), // simplified for ui response
                'dibuat_oleh' => $template->pembuat ? $template->pembuat->nama : 'Sistem',
                'tgl_dibuat' => $template->created_at ? $template->created_at->format('d M Y') : 'Baru saja',
            ]
        ]);
    }
}
