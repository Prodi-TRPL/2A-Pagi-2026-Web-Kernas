@extends('layouts.app')

@section('title', 'Editor Draf Surat — KERNAS')

@section('content')
<div class="h-[calc(100vh-64px)] flex flex-col bg-gray-50">
    <div class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between shrink-0">
        <div>
            <div class="flex items-center gap-3">
                <a href="/dashboard" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                </a>
                <h1 class="text-xl font-bold text-gray-900">Editor Draf Surat</h1>
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold 
                    {{ $pengajuan->status === 'POSTED' ? 'bg-amber-100 text-amber-700' : 
                       ($pengajuan->status === 'REVIEWED' ? 'bg-indigo-100 text-indigo-700' : 
                       ($pengajuan->status === 'PUBLISHED' ? 'bg-sky-100 text-sky-700' : 'bg-gray-100 text-gray-700')) }}">
                    {{ $pengajuan->status }}
                </span>
            </div>
            <p class="text-sm text-gray-500 mt-1 ml-10">Judul: <span class="font-medium text-gray-700">{{ $pengajuan->judul }}</span></p>
        </div>
        
        <div class="flex items-center gap-3">
            @if($pengajuan->ada_lampiran)
                <a href="/pengajuan/{{ $pengajuan->id }}/lampiran" class="px-3 py-1.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                    Lihat Lampiran
                </a>
                <div class="h-6 w-px bg-gray-300 mx-1"></div>
            @endif

            @if($pengajuan->status === 'POSTED' || $pengajuan->status === 'REJECTED')
                @if($isAdmin)
                    <div class="flex items-center gap-2">
                        @if($pengajuan->status === 'POSTED')
                        <div x-data="{ showModal: false }">
                            <button @click="showModal = true" type="button" class="px-4 py-2 text-sm font-semibold text-red-700 bg-red-100 rounded-lg hover:bg-red-200 transition-colors shadow-sm flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"/></svg>
                                Kembalikan ke Pengusul
                            </button>

                            <!-- Modal Pengembalian (Admin) -->
                            <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" x-cloak style="display: none;">
                                <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6" @click.away="showModal = false">
                                    <h3 class="text-lg font-bold text-gray-900 mb-2">Kembalikan ke Pengusul</h3>
                                    <p class="text-sm text-gray-500 mb-4">Berikan catatan revisi agar Pengusul dapat memperbaikinya sebelum Anda mengirimkannya ke Verifikator.</p>
                                    
                                    <form action="/pengajuan/{{ $pengajuan->id }}/admin-kembalikan" method="POST">
                                        @csrf
                                        <textarea name="catatan" rows="3" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 outline-none mb-4" placeholder="Contoh: Mohon perbaiki judul surat."></textarea>
                                        
                                        <div class="flex justify-end gap-3">
                                            <button type="button" @click="showModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Batal</button>
                                            <button type="submit" class="px-4 py-2 text-sm font-bold text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">Kembalikan Dokumen</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif

                        <form action="/pengajuan/{{ $pengajuan->id }}/admin-kirim" method="POST" onsubmit="return confirm('Tandai selesai di-review dan kirim dokumen ini ke Verifikator?');">
                            @csrf
                            <button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors shadow-sm flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/></svg>
                                Kirim ke Verifikator
                            </button>
                        </form>
                    </div>
                @endif
                @if($isProposer)
                    @if($pengajuan->status === 'REJECTED')
                        <form action="/pengajuan/{{ $pengajuan->id }}/hapus" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengajuan yang ditolak ini? Ini tidak bisa dikembalikan.');">
                            @csrf
                            <button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors shadow-sm flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                Hapus Dokumen
                            </button>
                        </form>
                    @else
                        <span class="text-xs text-gray-500 font-medium bg-gray-100 px-3 py-1.5 rounded-lg border border-gray-200">
                            Penyimpanan otomatis saat mengetik
                        </span>
                        <form action="/pengajuan/{{ $pengajuan->id }}/kirim" method="POST">
                            @csrf
                            <button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors shadow-sm flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                Simpan Draf & Keluar
                            </button>
                        </form>
                    @endif
                @endif
            @elseif($pengajuan->status === 'REVIEWED')
                @if($isVerifier)
                    <form action="/pengajuan/{{ $pengajuan->id }}/terima" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin MENYETUJUI dokumen ini?');">
                        @csrf
                        <button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-sky-600 rounded-lg hover:bg-sky-700 transition-colors shadow-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                            Setujui Dokumen
                        </button>
                    </form>
                    
                    <div x-data="{ showModal: false }">
                        <button @click="showModal = true" type="button" class="px-4 py-2 text-sm font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors shadow-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            Tolak Dokumen
                        </button>

                        <!-- Modal Penolakan -->
                        <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" x-cloak style="display: none;">
                            <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6" @click.away="showModal = false">
                                <h3 class="text-lg font-bold text-gray-900 mb-2">Tolak Dokumen</h3>
                                <p class="text-sm text-gray-500 mb-4">Silakan masukkan alasan atau catatan mengapa dokumen ini ditolak agar pengusul dapat memperbaikinya.</p>
                                
                                <form action="/pengajuan/{{ $pengajuan->id }}/tolak" method="POST">
                                    @csrf
                                    <textarea name="catatan" rows="3" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 outline-none mb-4" placeholder="Contoh: Format tabel masih salah, mohon diperbaiki."></textarea>
                                    
                                    <div class="flex justify-end gap-3">
                                        <button type="button" @click="showModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Batal</button>
                                        <button type="submit" class="px-4 py-2 text-sm font-bold text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">Konfirmasi Tolak</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <span class="text-xs text-amber-600 font-medium bg-amber-50 px-3 py-1.5 rounded-lg border border-amber-200">
                        Dokumen ini sedang ditinjau oleh Verifikator (Hanya Baca).
                    </span>
                @endif
            @elseif($pengajuan->status === 'REJECTED_BY_VERIFIER')
                @if($isAdmin)
                    <form action="/pengajuan/{{ $pengajuan->id }}/admin-kirim-ulang" method="POST" onsubmit="return confirm('Kirim ulang dokumen ini ke Verifikator yang sama?');">
                        @csrf
                        <button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors shadow-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/></svg>
                            Kirim Ulang ke Verifikator
                        </button>
                    </form>
                    
                    <div x-data="{ showModal: false }">
                        <button @click="showModal = true" type="button" class="px-4 py-2 text-sm font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors shadow-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"/></svg>
                            Kembalikan ke Pengusul
                        </button>

                        <!-- Modal Pengembalian -->
                        <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" x-cloak style="display: none;">
                            <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6" @click.away="showModal = false">
                                <h3 class="text-lg font-bold text-gray-900 mb-2">Kembalikan ke Pengusul</h3>
                                <p class="text-sm text-gray-500 mb-4">Silakan masukkan alasan agar pengusul tahu apa yang harus diperbaiki.</p>
                                
                                <form action="/pengajuan/{{ $pengajuan->id }}/admin-kembalikan" method="POST">
                                    @csrf
                                    <textarea name="catatan" rows="3" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 outline-none mb-4" placeholder="Contoh: Format tabel masih salah, mohon diperbaiki.">{{ $pengajuan->catatan }}</textarea>
                                    
                                    <div class="flex justify-end gap-3">
                                        <button type="button" @click="showModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Batal</button>
                                        <button type="submit" class="px-4 py-2 text-sm font-bold text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">Kembalikan Dokumen</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <span class="text-xs text-amber-600 font-medium bg-amber-50 px-3 py-1.5 rounded-lg border border-amber-200">
                        Dokumen ini sedang diperiksa oleh Admin (Hanya Baca).
                    </span>
                @endif
            @else
            <span class="text-xs text-amber-600 font-medium bg-amber-50 px-3 py-1.5 rounded-lg border border-amber-200">
                Dokumen ini tidak bisa diedit karena statusnya {{ $pengajuan->status }}.
            </span>
            @endif
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-50 text-green-700 px-6 py-3 border-b border-green-100 flex items-center gap-3 shrink-0">
        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span class="text-sm font-medium">{{ session('success') }}</span>
    </div>
    @endif

    <div class="flex-1 w-full flex overflow-hidden">
        <div class="flex-1 bg-gray-200 relative">
            <div id="placeholder" class="absolute inset-0"></div>
        </div>

        @if($pengajuan->catatan && in_array($pengajuan->status, ['REJECTED', 'REJECTED_BY_VERIFIER']))
        <div class="w-80 bg-red-50 border-l border-red-200 flex flex-col shrink-0 z-10 shadow-sm relative">
            <div class="p-4 border-b border-red-200 bg-red-100 flex items-center gap-2 shrink-0">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <h2 class="text-sm font-bold text-red-800 uppercase tracking-wider">Catatan Penolakan</h2>
            </div>
            <div class="p-4 overflow-y-auto flex-1">
                <p class="text-sm text-red-800 bg-white p-3.5 rounded-xl border border-red-100 shadow-sm whitespace-pre-wrap leading-relaxed">{{ $pengajuan->catatan }}</p>
                
                <div class="mt-4 p-3 bg-red-100/50 rounded-lg">
                    <p class="text-xs text-red-700 font-medium">Mohon perbaiki dokumen Anda sesuai instruksi di atas sebelum mengirimkannya kembali.</p>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<script src="http://localhost:8080/web-apps/apps/api/documents/api.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const config = @js($config);
        const docEditor = new DocsAPI.DocEditor("placeholder", config);
    });
</script>
@endsection
