<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengguna;
use Illuminate\Support\Facades\DB;

class PegawaiController extends Controller
{
    // (alur data: fungsi ini mengambil data karyawan dan grup verifikasi menggunakan model pengguna. data tersebut akan dikirim dan digunakan di view 'pegawai' pada bagian javascript (alpine) di baris 380)
    public function index()
    {
        if (!session()->has('pengguna')) return redirect('/');

        $penggunas = Pengguna::where('is_deleted', 0)->with('grupVerifikasi')->get();
        $grupVerifikasi = DB::table('grup_verifikasi')->select('id', 'nama_grup as nama')->get();
        
        $karyawan = $penggunas->map(function($p) {
            $isVerifikator = $p->grupVerifikasi->isNotEmpty();
            $role = $p->is_admin ? 'admin' : ($isVerifikator ? 'verifikator' : 'staff');
            $grup_id = null;
            if ($isVerifikator) {
                $grup = $p->grupVerifikasi->first();
                if ($grup) {
                    $grup_id = $grup->pivot->id_grup_verifikasi;
                }
            }
            return [
                'id' => $p->id,
                'nip' => $p->nip,
                'nama' => $p->nama,
                'jabatan' => $p->jabatan,
                'unit' => $p->unit,
                'role' => $role,
                'grup_id' => $grup_id
            ];
        });
        
        return view('pegawai', compact('karyawan', 'grupVerifikasi'));
    }

    public function store(Request $request)
    {
        if (!session()->has('pengguna')) return response()->json(['error' => 'Unauthorized'], 401);

        $request->validate([
            'nip' => 'required|string|unique:pengguna,nip',
            'username' => 'required|string|unique:pengguna,username',
            'password' => 'required|string|min:4',
            'nama' => 'required|string|max:100',
            'jabatan' => 'nullable|string|max:100',
            'unit' => 'required|string|max:100'
        ]);

        $pengguna = Pengguna::create([
            'nip' => $request->nip,
            'username' => $request->username,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'nama' => $request->nama,
            'jabatan' => $request->jabatan,
            'unit' => $request->unit,
            'is_admin' => 0
        ]);

        return response()->json([
            'message' => 'Pengguna berhasil ditambahkan',
            'pengguna' => [
                'id' => $pengguna->id,
                'nip' => $pengguna->nip,
                'nama' => $pengguna->nama,
                'jabatan' => $pengguna->jabatan,
                'unit' => $pengguna->unit,
                'role' => 'staff',
                'grup_id' => null
            ]
        ]);
    }

    // (alur data: fungsi ini menerima data inputan profil dari modal edit profil di view 'pegawai' baris 330, memvalidasinya, lalu menyimpannya ke database menggunakan model pengguna)
    public function updateProfil(Request $request, $id)
    {
        if (!session()->has('pengguna')) return response()->json(['error' => 'Unauthorized'], 401);
        
        $request->validate([
            'nama' => 'required|string|max:100',
            'jabatan' => 'nullable|string|max:100',
            'unit' => 'required|string|max:100'
        ]);

        Pengguna::where('id', $id)->update([
            'nama' => $request->nama,
            'jabatan' => $request->jabatan,
            'unit' => $request->unit
        ]);

        return response()->json(['message' => 'Profil berhasil diperbarui']);
    }

    // menggunakan fitur "sync" untuk edit tabel sangatlah berguna (reminder to use it more often on next project)
    // (alur data: fungsi ini menerima aksi simpan dari modal atur peran di view 'pegawai' baris 204, lalu memperbarui peran pengguna di database menggunakan model pengguna) (lupakan ini)
    public function updatePeran(Request $request, $id)
    {
        if (!session()->has('pengguna')) return response()->json(['error' => 'Unauthorized'], 401);
        
        $role = $request->role;
        $grup_id = $request->grup_id;
        $pengguna = Pengguna::findOrFail($id);
        
        if ($role === 'admin') {
            $pengguna->update(['is_admin' => 1]);
            $pengguna->grupVerifikasi()->sync([]);
        } elseif ($role === 'verifikator') {
            $pengguna->update(['is_admin' => 0]);
            if ($grup_id) {
                $pengguna->grupVerifikasi()->sync([$grup_id]);
            } else {
                $pengguna->grupVerifikasi()->sync([]);
            }
        } else {
            $pengguna->update(['is_admin' => 0]);
            $pengguna->grupVerifikasi()->sync([]);
        }

        return response()->json(['message' => 'Peran berhasil diperbarui']);
    }

    public function destroy($id)
    {
        if (!session()->has('pengguna')) return response()->json(['error' => 'Unauthorized'], 401);
        $penggunaLokal = session('pengguna');
        if (empty($penggunaLokal['is_admin'])) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $pengguna = Pengguna::findOrFail($id);
        $pengguna->update(['is_deleted' => 1]);
        
        return response()->json(['message' => 'Pegawai berhasil dihapus']);
    }
}
