<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peraturan;

class PeraturanController extends Controller
{
    // ini untuk mengambil daftar peraturan beserta nama penguploadnya untuk ditampilkan di view 'peraturan'
    public function index()
    {
        if (!session()->has('pengguna')) return redirect('/');
        
        $peraturan = Peraturan::leftJoin('pengguna', 'peraturan.id_pengguna', '=', 'pengguna.id')
            ->select('peraturan.*', 'pengguna.nama as ditambah_oleh')
            ->orderBy('id', 'desc')
            ->get();
            
        return view('peraturan', compact('peraturan'));
    }

    // ini untuk menyimpan data peraturan baru yang diinputkan pengguna ke dalam database
    public function store(Request $request)
    {
        if (!session()->has('pengguna')) return response()->json(['error' => 'Unauthorized'], 401);
        $user = session('pengguna');

        $request->validate([
            'kode' => 'required|string|max:100|unique:peraturan,kode',
            'judul' => 'required|string|max:255',
            'jenis' => 'required|string|max:50',
            'tahun' => 'required|integer',
        ]);

        $peraturan = Peraturan::create([
            'id_pengguna' => $user['id'],
            'kode' => $request->kode,
            'judul' => $request->judul,
            'jenis' => $request->jenis,
            'tahun' => $request->tahun,
            'keterangan' => $request->keterangan,
        ]);

        $peraturan->ditambah_oleh = $user['nama'];

        return response()->json(['success' => true, 'data' => $peraturan]);
    }

    // ini untuk memperbarui data peraturan yang sudah ada di database berdasarkan inputan pengguna
    public function update(Request $request, $id)
    {
        if (!session()->has('pengguna')) return response()->json(['error' => 'Unauthorized'], 401);

        $request->validate([
            'kode' => 'required|string|max:100|unique:peraturan,kode,'.$id,
            'judul' => 'required|string|max:255',
            'jenis' => 'required|string|max:50',
            'tahun' => 'required|integer',
        ]);

        $peraturan = Peraturan::findOrFail($id);
        $peraturan->update([
            'kode' => $request->kode,
            'judul' => $request->judul,
            'jenis' => $request->jenis,
            'tahun' => $request->tahun,
            'keterangan' => $request->keterangan,
        ]);

        return response()->json(['success' => true, 'data' => $peraturan]);
    }

    // ini untuk menghapus data peraturan secara permanen dari database
    public function destroy($id)
    {
        if (!session()->has('pengguna')) return response()->json(['error' => 'Unauthorized'], 401);

        $peraturan = Peraturan::findOrFail($id);
        $peraturan->delete();

        return response()->json(['success' => true]);
    }
}
