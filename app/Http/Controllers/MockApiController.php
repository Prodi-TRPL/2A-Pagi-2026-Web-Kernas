<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MockApiController extends Controller
{
    // ─────────────────────────────────────────────────────────────────
    // DATA PALSU — format identik dengan response API Polibatam asli
    // ─────────────────────────────────────────────────────────────────

    private array $pegawai = [
        [
            'id'     => '001',
            'NIP'    => '001',
            'nama'   => 'Ahmad Fauzi',
            'unit'   => 'Teknik Informatika',
            'jabatan'=> 'Dosen',
            'email'  => 'ahmad.fauzi@polibatam.ac.id',
        ],
        [
            'id'     => '002',
            'NIP'    => '002',
            'nama'   => 'Budi Santoso',
            'unit'   => 'Teknik Informatika',
            'jabatan'=> 'Dosen',
            'email'  => 'budi.santoso@polibatam.ac.id',
        ],
        [
            'id'     => '003',
            'NIP'    => '003',
            'nama'   => 'Cici Rahayu',
            'unit'   => 'Sistem Informasi',
            'jabatan'=> 'Dosen',
            'email'  => 'cici.rahayu@polibatam.ac.id',
        ],
        [
            'id'     => '004',
            'NIP'    => '004',
            'nama'   => 'Dodi Pratama',
            'unit'   => 'Manajemen',
            'jabatan'=> 'Staff',
            'email'  => 'dodi.pratama@polibatam.ac.id',
        ],
        [
            'id'     => '005',
            'NIP'    => '005',
            'nama'   => 'Eka Putri',
            'unit'   => 'Teknik Mesin',
            'jabatan'=> 'Dosen',
            'email'  => 'eka.putri@polibatam.ac.id',
        ],
        [
            'id'     => '006',
            'NIP'    => '006',
            'nama'   => 'Fajar Nugroho',
            'unit'   => 'Teknik Elektro',
            'jabatan'=> 'Staff',
            'email'  => 'fajar.nugroho@polibatam.ac.id',
        ],
        [
            'id'     => '007',
            'NIP'    => '007',
            'nama'   => 'Gita Lestari',
            'unit'   => 'Keuangan',
            'jabatan'=> 'Staff',
            'email'  => 'gita.lestari@polibatam.ac.id',
        ],
        [
            'id'     => '008',
            'NIP'    => '008',
            'nama'   => 'Hendra Wijaya',
            'unit'   => 'Teknik Informatika',
            'jabatan'=> 'Dosen',
            'email'  => 'hendra.wijaya@polibatam.ac.id',
        ],
        [
            'id'     => '009',
            'NIP'    => '009',
            'nama'   => 'Indah Permata',
            'unit'   => 'Sistem Informasi',
            'jabatan'=> 'Staff',
            'email'  => 'indah.permata@polibatam.ac.id',
        ],
        [
            'id'     => '010',
            'NIP'    => '010',
            'nama'   => 'Joko Susilo',
            'unit'   => 'Teknik Mesin',
            'jabatan'=> 'Dosen',
            'email'  => 'joko.susilo@polibatam.ac.id',
        ],
    ];

    private array $units = [
        ['id' => 'TI',  'nama' => 'Teknik Informatika'],
        ['id' => 'SI',  'nama' => 'Sistem Informasi'],
        ['id' => 'MN',  'nama' => 'Manajemen'],
        ['id' => 'TM',  'nama' => 'Teknik Mesin'],
        ['id' => 'TE',  'nama' => 'Teknik Elektro'],
        ['id' => 'KEU', 'nama' => 'Keuangan'],
    ];

    // Akun yang bisa login (username => data)
    private array $accounts = [
        'ahmad'  => ['NIP' => '001', 'password' => 'password'],
        'budi'   => ['NIP' => '002', 'password' => 'password'],
        'cici'   => ['NIP' => '003', 'password' => 'password'],
        'dodi'   => ['NIP' => '004', 'password' => 'password'],
        'eka'    => ['NIP' => '005', 'password' => 'password'],
        'fajar'  => ['NIP' => '006', 'password' => 'password'],
        'gita'   => ['NIP' => '007', 'password' => 'password'],
        'hendra' => ['NIP' => '008', 'password' => 'password'],
        'indah'  => ['NIP' => '009', 'password' => 'password'],
        'joko'   => ['NIP' => '010', 'password' => 'password'],
    ];

    // ─────────────────────────────────────────────────────────────────
    // SINGLE ENTRY POINT — POST /api/mock
    // Meniru perilaku API Polibatam asli yang pakai satu endpoint
    // dengan parameter "act" untuk menentukan aksi
    // ─────────────────────────────────────────────────────────────────

    public function handle(Request $request)
    {
        $act = $request->input('act');

        return match ($act) {
            'Login'          => $this->login($request),
            'GetBiodata'     => $this->getBiodata($request),
            'GetToken'       => $this->getToken($request),
            'GetSemuaUnit'   => $this->getSemuaUnit($request),
            'GetSemuaPegawai'=> $this->getSemuaPegawai($request),
            'GetDataByID'    => $this->getDataByID($request),
            default          => $this->error("Aksi tidak dikenal: {$act}", 400),
        };
    }

    // ─────────────────────────────────────────────────────────────────
    // act: Login
    // Input : username, password
    // Output: secretkey (digunakan untuk semua call berikutnya)
    // ─────────────────────────────────────────────────────────────────
    private function login(Request $request)
    {
        $username = $request->input('username');
        $password = $request->input('password');

        if (!$username || !$password) {
            return response()->json([
                'error_code' => 102,
                'error_desc' => 'Username dan password wajib diisi',
                'data'       => null,
            ]);
        }

        $account = $this->accounts[$username] ?? null;

        if (!$account || $account['password'] !== $password) {
            return response()->json([
                'error_code' => 102,
                'error_desc' => 'Username atau password salah',
                'data'       => null,
            ]);
        }

        // secretkey = "mock_" + NIP + "_" + timestamp (meniru format asli)
        $secretkey = 'mock_' . $account['NIP'] . '_' . time();

        // Simpan secretkey → NIP di session/cache supaya GetBiodata bisa resolve
        // Di production asli ini dihandle oleh server Polibatam
        // Di mock kita simpan di Laravel cache
        cache()->put("mock_sk_{$secretkey}", $account['NIP'], now()->addHours(8));

        return response()->json([
            'error_code' => 0,
            'error_desc' => 'OK',
            'data'       => [
                'secretkey' => $secretkey,
            ],
        ]);
    }

    // ─────────────────────────────────────────────────────────────────
    // act: GetBiodata
    // Input : secretkey
    // Output: data profil pengguna yang sedang login
    // ─────────────────────────────────────────────────────────────────
    private function getBiodata(Request $request)
    {
        $secretkey = $request->input('secretkey');

        if (!$secretkey) {
            return response()->json([
                'error_code' => 101,
                'error_desc' => 'Secretkey tidak ditemukan',
                'data'       => null,
            ]);
        }

        $nip = cache()->get("mock_sk_{$secretkey}");

        if (!$nip) {
            return response()->json([
                'error_code' => 101,
                'error_desc' => 'Secretkey tidak valid atau sudah expired',
                'data'       => null,
            ]);
        }

        $pegawai = collect($this->pegawai)->firstWhere('NIP', $nip);

        if (!$pegawai) {
            return response()->json([
                'error_code' => 101,
                'error_desc' => 'Data pegawai tidak ditemukan',
                'data'       => null,
            ]);
        }

        return response()->json([
            'error_code' => 0,
            'error_desc' => 'OK',
            'data'       => $pegawai,
            // Format identik dengan API asli:
            // data.id   = NIP pegawai (dipakai sebagai user identifier)
            // data.NIP  = NIP pegawai
            // data.nama = nama lengkap
        ]);
    }

    // ─────────────────────────────────────────────────────────────────
    // act: GetToken
    // Input : secretkey
    // Output: token (digunakan untuk query GetSemuaPegawai, GetDataByID)
    // ─────────────────────────────────────────────────────────────────
    private function getToken(Request $request)
    {
        $secretkey = $request->input('secretkey');

        if (!$secretkey || !cache()->has("mock_sk_{$secretkey}")) {
            return response()->json([
                'error_code' => 101,
                'error_desc' => 'Secretkey tidak valid',
                'data'       => null,
            ]);
        }

        // Token untuk query data pegawai (berbeda dari secretkey login)
        $token = 'mock_token_' . md5($secretkey . now()->timestamp);
        cache()->put("mock_token_{$token}", true, now()->addMinutes(30));

        return response()->json([
            'error_code' => 0,
            'error_desc' => 'OK',
            'data'       => [
                'token' => $token,
            ],
        ]);
    }

    // ─────────────────────────────────────────────────────────────────
    // act: GetSemuaUnit
    // Input : token
    // Output: daftar semua unit/departemen di Polibatam
    // ─────────────────────────────────────────────────────────────────
    private function getSemuaUnit(Request $request)
    {
        $token = $request->input('token');

        if (!$token || !cache()->has("mock_token_{$token}")) {
            return response()->json([
                'error_code' => 101,
                'error_desc' => 'Token tidak valid',
                'data'       => null,
            ]);
        }

        return response()->json([
            'error_code' => 0,
            'error_desc' => 'OK',
            'data'       => $this->units,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────
    // act: GetSemuaPegawai
    // Input : token, filter (format: "unit=Teknik Informatika")
    // Output: daftar pegawai dalam unit tersebut
    // ─────────────────────────────────────────────────────────────────
    private function getSemuaPegawai(Request $request)
    {
        $token  = $request->input('token');
        $filter = $request->input('filter', '');

        if (!$token || !cache()->has("mock_token_{$token}")) {
            return response()->json([
                'error_code' => 101,
                'error_desc' => 'Token tidak valid',
                'data'       => null,
            ]);
        }

        $pegawai = collect($this->pegawai);

        // Parse filter "unit=Teknik Informatika"
        if ($filter) {
            parse_str(str_replace(',', '&', $filter), $filters);
            if (!empty($filters['unit'])) {
                $pegawai = $pegawai->filter(
                    fn($p) => strtolower($p['unit']) === strtolower($filters['unit'])
                )->values();
            }
        }

        return response()->json([
            'error_code' => 0,
            'error_desc' => 'OK',
            'data'       => $pegawai->values(),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────
    // act: GetDataByID
    // Input : token, filter (format: "nip=001")
    // Output: data satu pegawai berdasarkan NIP
    // ─────────────────────────────────────────────────────────────────
    private function getDataByID(Request $request)
    {
        $token  = $request->input('token');
        $filter = $request->input('filter', '');

        if (!$token || !cache()->has("mock_token_{$token}")) {
            return response()->json([
                'error_code' => 101,
                'error_desc' => 'Token tidak valid',
                'data'       => null,
            ]);
        }

        // Parse filter "nip=001"
        parse_str(str_replace(',', '&', $filter), $filters);
        $nip = $filters['nip'] ?? null;

        if (!$nip) {
            return response()->json([
                'error_code' => 400,
                'error_desc' => 'Parameter nip wajib diisi',
                'data'       => [],
            ]);
        }

        $pegawai = collect($this->pegawai)->filter(
            fn($p) => $p['NIP'] === $nip
        )->values();

        return response()->json([
            'error_code' => 0,
            'error_desc' => 'OK',
            // API asli return array (bisa lebih dari 1 hasil)
            // pegawai.Controller.js pakai result.data.data[0]
            'data'       => $pegawai,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────
    // HELPER
    // ─────────────────────────────────────────────────────────────────
    private function error(string $message, int $status = 400)
    {
        return response()->json([
            'error_code' => $status,
            'error_desc' => $message,
            'data'       => null,
        ], $status);
    }
}
