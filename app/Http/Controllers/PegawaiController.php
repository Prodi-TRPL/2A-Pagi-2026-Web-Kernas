<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengguna;
use Illuminate\Support\Facades\DB;

class PegawaiController extends Controller
{
    // ini untuk mengambil data karyawan dan grup verifikasi dari model lalu mengirimkannya ke view 'pegawai'
    public function index()
    {
        if (!session()->has('pengguna')) return redirect('/');
        $penggunaLokal = session('pengguna');

        // Hanya admin yang boleh melihat dan mengelola daftar karyawan
        if (empty($penggunaLokal['is_admin'])) {
            return redirect('/dashboard')->with('error', 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

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
                'id'      => $p->id,
                'nip'     => $p->nip,
                'nama'    => $p->nama,
                'jabatan' => $p->jabatan,
                'unit'    => $p->unit,
                'role'    => $role,
                'grup_id' => $grup_id
            ];
        });
        
        return view('pegawai', compact('karyawan', 'grupVerifikasi'));
    }

    // ini untuk menyimpan data pengguna (karyawan) baru ke dalam database
    public function store(Request $request)
    {
        if (!session()->has('pengguna')) return response()->json(['error' => 'Unauthorized'], 401);
        $penggunaLokal = session('pengguna');

        // Hanya admin yang boleh menambah karyawan baru
        if (empty($penggunaLokal['is_admin'])) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $request->validate([
            'nip'      => 'required|string|unique:pengguna,nip',
            'username' => 'required|string|unique:pengguna,username',
            'password' => 'required|string|min:4',
            'nama'     => 'required|string|max:100',
            'jabatan'  => 'nullable|string|max:100',
            'unit'     => 'required|string|max:100'
        ]);

        $pengguna = Pengguna::create([
            'nip'      => $request->nip,
            'username' => $request->username,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'nama'     => $request->nama,
            'jabatan'  => $request->jabatan,
            'unit'     => $request->unit,
            'is_admin' => 0  // Selalu 0 saat create
        ]);

        return response()->json([
            'message'  => 'Pengguna berhasil ditambahkan',
            'pengguna' => [
                'id'      => $pengguna->id,
                'nip'     => $pengguna->nip,
                'nama'    => $pengguna->nama,
                'jabatan' => $pengguna->jabatan,
                'unit'    => $pengguna->unit,
                'role'    => 'staff',
                'grup_id' => null
            ]
        ]);
    }

    // ini untuk menerima inputan profil dari modal edit, memvalidasinya, lalu menyimpannya ke database via model Pengguna
    public function updateProfil(Request $request, $id)
    {
        if (!session()->has('pengguna')) return response()->json(['error' => 'Unauthorized'], 401);
        $penggunaLokal = session('pengguna');

        // Hanya admin yang boleh edit profil orang lain (atau user sendiri)
        $isSelf = ((int)$penggunaLokal['id'] === (int)$id);
        if (empty($penggunaLokal['is_admin']) && !$isSelf) {
            return response()->json(['error' => 'Forbidden'], 403);
        }
        
        $request->validate([
            'nama'    => 'required|string|max:100',
            'jabatan' => 'nullable|string|max:100',
            'unit'    => 'required|string|max:100'
        ]);

        Pengguna::where('id', $id)->update([
            'nama'    => $request->nama,
            'jabatan' => $request->jabatan,
            'unit'    => $request->unit
        ]);

        return response()->json(['message' => 'Profil berhasil diperbarui']);
    }

    // menggunakan fitur "sync" untuk edit tabel sangatlah berguna (reminder to use it more often on next project)
    // ini untuk menerima aksi simpan peran (admin/verifikator/staff) dari modal dan memperbaruinya di database
    public function updatePeran(Request $request, $id)
    {
        if (!session()->has('pengguna')) return response()->json(['error' => 'Unauthorized'], 401);
        $penggunaLokal = session('pengguna');

        // Hanya admin yang boleh mengubah peran pengguna lain
        if (empty($penggunaLokal['is_admin'])) {
            return response()->json(['error' => 'Forbidden'], 403);
        }
        
        $role    = $request->role;
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

    // ini untuk melakukan soft delete pada akun pegawai dan menghapus relasinya dari grup verifikasi
    public function destroy($id)
    {
        if (!session()->has('pengguna')) return response()->json(['error' => 'Unauthorized'], 401);
        $penggunaLokal = session('pengguna');
        if (empty($penggunaLokal['is_admin'])) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $pengguna = Pengguna::findOrFail($id);
        
        // Hapus pengguna dari semua grup verifikasi agar tidak nyangkut
        $pengguna->grupVerifikasi()->detach();
        
        $pengguna->update(['is_deleted' => 1]);
        
        return response()->json(['message' => 'Pegawai berhasil dihapus']);
    }
}
