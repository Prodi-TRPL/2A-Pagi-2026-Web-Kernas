<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TemplateSurat;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class OnlyOfficeController extends Controller
{
    public function callback(Request $request, $id)
    {
        $body = $request->all();

        // Status 2 = Dokumen ditutup dan siap disimpan
        // Status 6 = Dokumen sedang diedit, tapi user menekan Save (forcesave)
        // Status 3 = Save error
        $status = $body['status'] ?? 0;
        
        if ($status == 2 || $status == 6) {
            $downloadUri = $body['url'];
            
            // Docker ONLYOFFICE mungkin mengirim URL internalnya sendiri (misal http://172.17.0.2/...)
            // Laravel (Host) tidak bisa mengakses IP internal Docker di Windows, jadi kita ubah domainnya ke localhost:8080
            $downloadUri = preg_replace('/^http:\/\/[^\/]+/', 'http://127.0.0.1:8080', $downloadUri);
            
            try {
                // Unduh file dari Document Server
                $newFileData = file_get_contents($downloadUri);
                
                if ($newFileData === false) {
                    throw new \Exception("Gagal mengunduh file dari $downloadUri");
                }

                $template = TemplateSurat::findOrFail($id);
                
                // Timpa file lama dengan file baru
                Storage::disk('public')->put($template->filepath, $newFileData);
                
            } catch (\Exception $e) {
                Log::error('ONLYOFFICE Callback Error: ' . $e->getMessage());
                return response()->json(['error' => 1, 'message' => $e->getMessage()]);
            }
        }

        // Harus me-return {"error": 0} agar ONLYOFFICE tahu callback sukses
        return response()->json(['error' => 0]);
    }

    public function callbackPengajuan(Request $request, $id)
    {
        $body = $request->all();
        $status = $body['status'] ?? 0;
        
        if ($status == 2 || $status == 6) {
            $downloadUri = $body['url'];
            $downloadUri = preg_replace('/^http:\/\/[^\/]+/', 'http://127.0.0.1:8080', $downloadUri);
            
            try {
                $newFileData = file_get_contents($downloadUri);
                if ($newFileData === false) throw new \Exception("Gagal mengunduh file");

                $pengajuan = \App\Models\Pengajuan::findOrFail($id);
                Storage::disk('public')->put($pengajuan->filepath, $newFileData);
                
            } catch (\Exception $e) {
                Log::error('ONLYOFFICE Callback Pengajuan Error: ' . $e->getMessage());
                return response()->json(['error' => 1, 'message' => $e->getMessage()]);
            }
        }
        return response()->json(['error' => 0]);
    }
}
