<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengguna;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function showLogin()
    {
        // chek sytem unutk loggin (ubah jika ada cara yang lebih "clean")
        if (session()->has('pengguna')) {
            return redirect('/dashboard');
        }
        return view('login');
    }

    // (alur data: fungsi ini menerima aksi submit dari form login di view 'login' baris 78, memvalidasi datanya menggunakan model pengguna, lalu menyimpannya ke session)
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $pengguna = Pengguna::where('username', $request->username)->first();

        if ($pengguna && Hash::check($request->password, $pengguna->password)) {
            
            session([
                'pengguna' => [
                    'id' => $pengguna->id,
                    'nip' => $pengguna->nip,
                    'nama' => $pengguna->nama,
                    'unit' => $pengguna->unit,
                    'is_admin' => $pengguna->is_admin,
                    'is_verifikator' => $pengguna->isVerifikator(),
                    'grup_ids' => $pengguna->getGrupIds()
                ]
            ]);

            return redirect('/dashboard');
        }

        return back()->with('error', 'Username atau password salah. Pastikan menggunakan akun DokPol Anda.')->withInput();
    }
    
    public function logout()
    {
        session()->forget('pengguna');
        return redirect('/');
    }
}
