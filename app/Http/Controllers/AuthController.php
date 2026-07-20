<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengguna;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    // ini untuk menampilkan halaman form login (view 'login') atau mengalihkan ke dashboard jika sudah login
    public function showLogin()
    {
        // chek sytem unutk loggin (ubah jika ada cara yang lebih "clean")
        if (session()->has('pengguna')) {
            return redirect('/dashboard');
        }
        return view('login');
    }

    // ini untuk menerima aksi submit dari form login, memvalidasi data menggunakan model Pengguna, dan menyimpan info ke session
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $pengguna = Pengguna::where('username', $request->username)
                            ->where('is_deleted', 0)
                            ->first();

        if ($pengguna && Hash::check($request->password, $pengguna->password)) {
            
            session([
                'pengguna' => [
                    'id' => $pengguna->id,
                    'nip' => $pengguna->nip,
                    'nama' => $pengguna->nama,
                    'jabatan' => $pengguna->jabatan,
                    'unit' => $pengguna->unit,
                    'is_admin' => $pengguna->is_admin,
                    'is_verifikator' => $pengguna->isVerifikator(),
                    'grup_ids' => $pengguna->getGrupIds()
                ]
            ]);

            return redirect('/dashboard');
        }

        return back()->with('error', 'Username atau password salah.')->withInput();
    }
    
    // ini untuk menghapus sesi login pengguna dan mengarahkannya kembali ke halaman awal
    public function logout()
    {
        session()->forget('pengguna');
        return redirect('/');
    }
}
