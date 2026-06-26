<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GrupVerifikasi;
use App\Models\Pengguna;

class GrupVerifikasiController extends Controller
{
    public function index()
    {
        if (!session()->has('pengguna')) return redirect('/');

        // Jangan tampilkan grup Ad-Hoc
        $grups = GrupVerifikasi::with('pengguna')
            ->where('nama_grup', 'not like', 'Ad-Hoc:%')
            ->orderBy('id', 'desc')
            ->get();
        // Ambil data karyawan yang bukan admin super untuk bisa dijadikan anggota
        $karyawan = Pengguna::where('is_admin', 0)->get(['id', 'nama', 'nip', 'jabatan', 'unit']);

        return view('grup_verifikasi', compact('grups', 'karyawan'));
    }

    public function store(Request $request)
    {
        if (!session()->has('pengguna')) return response()->json(['error' => 'Unauthorized'], 401);

        $request->validate([
            'nama_grup' => 'required|string|max:100',
            'tingkat' => 'required|in:1,2,3'
        ]);

        $grup = GrupVerifikasi::create([
            'nama_grup' => $request->nama_grup,
            'tingkat' => $request->tingkat,
            'id_pengguna' => session('pengguna')['id']
        ]);

        $grup->load('pengguna');

        return response()->json(['message' => 'Grup berhasil dibuat', 'grup' => $grup]);
    }

    public function update(Request $request, $id)
    {
        if (!session()->has('pengguna')) return response()->json(['error' => 'Unauthorized'], 401);

        $request->validate([
            'nama_grup' => 'required|string|max:100',
            'tingkat' => 'required|in:1,2,3'
        ]);

        $grup = GrupVerifikasi::findOrFail($id);
        $grup->update([
            'nama_grup' => $request->nama_grup,
            'tingkat' => $request->tingkat
        ]);

        $grup->load('pengguna');

        return response()->json(['message' => 'Grup berhasil diperbarui', 'grup' => $grup]);
    }

    public function destroy($id)
    {
        if (!session()->has('pengguna')) return response()->json(['error' => 'Unauthorized'], 401);

        $grup = GrupVerifikasi::findOrFail($id);
        // Hapus relasi anggotanya juga
        $grup->pengguna()->detach();
        $grup->delete();

        return response()->json(['message' => 'Grup berhasil dihapus']);
    }

    public function addAnggota(Request $request, $id)
    {
        if (!session()->has('pengguna')) return response()->json(['error' => 'Unauthorized'], 401);

        $request->validate([
            'id_pengguna' => 'required|exists:pengguna,id'
        ]);

        $grup = GrupVerifikasi::findOrFail($id);
        $grup->pengguna()->syncWithoutDetaching([$request->id_pengguna]);
        
        $pengguna = Pengguna::find($request->id_pengguna);

        return response()->json(['message' => 'Anggota berhasil ditambahkan', 'pengguna' => $pengguna]);
    }

    public function removeAnggota($id, $id_pengguna)
    {
        if (!session()->has('pengguna')) return response()->json(['error' => 'Unauthorized'], 401);

        $grup = GrupVerifikasi::findOrFail($id);
        $grup->pengguna()->detach($id_pengguna);

        return response()->json(['message' => 'Anggota berhasil dihapus dari grup']);
    }
}
