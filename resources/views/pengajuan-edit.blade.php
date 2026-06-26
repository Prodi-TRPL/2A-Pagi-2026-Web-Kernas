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
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold whitespace-nowrap inline-block
                    {{ $pengajuan->status === 'Diproses Admin' ? 'bg-amber-100 text-amber-700' : 
                       ($pengajuan->status === 'Menunggu Verifikasi' ? 'bg-indigo-100 text-indigo-700' : 
                       ($pengajuan->status === 'Diterbitkan' ? 'bg-sky-100 text-sky-700' : 
                       (in_array($pengajuan->status, ['Ditolak Admin', 'Revisi']) ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700'))) }}">
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
            @endif

            <div x-data="{ showRiwayat: false }">
                <button @click="showRiwayat = true" type="button" class="px-3 py-1.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Riwayat
                </button>

                <!-- Modal Riwayat -->
                <div x-show="showRiwayat" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" x-cloak style="display: none;">
                    <div class="bg-white rounded-xl shadow-lg w-full max-w-lg max-h-[80vh] flex flex-col" @click.away="showRiwayat = false">
                        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                            <h3 class="text-lg font-bold text-gray-900">Riwayat Pengajuan</h3>
                            <button @click="showRiwayat = false" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <div class="p-6 overflow-y-auto flex-1">
                            <div class="space-y-6 relative before:absolute before:inset-0 before:ml-5 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-slate-300 before:to-transparent">
                                @foreach($riwayat as $log)
                                <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                                    <!-- Icon -->
                                    <div class="flex items-center justify-center w-10 h-10 rounded-full border-4 border-white {{ 
                                        $log->aksi == 'DIBUAT' ? 'bg-blue-500' : 
                                        ($log->aksi == 'DIREVIU' ? 'bg-amber-500' : 
                                        ($log->aksi == 'DISETUJUI' ? 'bg-green-500' : 
                                        ($log->aksi == 'DITERBITKAN' ? 'bg-sky-500' : 
                                        ($log->aksi == 'DITOLAK' ? 'bg-red-500' : 'bg-gray-500')))) 
                                    }} text-white shadow shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2 z-10">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            @if($log->aksi == 'DIBUAT')
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                                            @elseif($log->aksi == 'DIREVIU')
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                            @elseif($log->aksi == 'DISETUJUI')
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                                            @elseif($log->aksi == 'DITERBITKAN')
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            @elseif($log->aksi == 'DITOLAK')
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                                            @endif
                                        </svg>
                                    </div>
                                    
                                    <!-- Card -->
                                    <div class="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] p-4 rounded-xl border border-slate-200 bg-white shadow-sm">
                                        <div class="flex items-center justify-between space-x-2 mb-1">
                                            <div class="font-bold text-slate-900 text-sm">{{ $log->aksi }}</div>
                                            <time class="font-caveat font-medium text-xs text-indigo-500">{{ \Carbon\Carbon::parse($log->created_at)->diffForHumans() }}</time>
                                        </div>
                                        <div class="text-xs text-slate-500 mb-2">Oleh: <span class="font-semibold">{{ $log->pengguna->nama ?? 'Sistem' }}</span></div>
                                        <div class="text-slate-700 text-sm">
                                            {{ $log->catatan_aksi }}
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="h-6 w-px bg-gray-300 mx-1"></div>

            @if($pengajuan->status === 'Diproses Admin' || $pengajuan->status === 'Ditolak Admin')
                @if($isAdmin)
                    <div class="flex items-center gap-2">
                        @if($pengajuan->status === 'Diproses Admin')
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

                        <!-- Modal Pemilihan Verifikator -->
                        <div x-data="{ showVerifikatorModal: false }">
                            <button @click="showVerifikatorModal = true" type="button" class="px-4 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors shadow-sm flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/></svg>
                                Kirim ke Verifikator
                            </button>

                            <!-- Backdrop & Modal Content -->
                            <div x-show="showVerifikatorModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
                                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                    <div x-show="showVerifikatorModal" 
                                         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
                                         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
                                         class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>

                                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                                    <div x-show="showVerifikatorModal" @click.away="showVerifikatorModal = false"
                                         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                                         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                                         class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-xl shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                                        <div class="sm:flex sm:items-start">
                                            <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-indigo-100 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                            </div>
                                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                                <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">Pilih Verifikator</h3>
                                                <div class="mt-2 text-sm text-gray-500">
                                                    Pilih siapa saja yang akan memverifikasi dan menandatangani dokumen ini.
                                                </div>
                                            </div>
                                        </div>

                                        <form action="/pengajuan/{{ $pengajuan->id }}/admin-kirim" method="POST" class="mt-5 space-y-4">
                                            @csrf
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-800 mb-1">Verifikator 1 <span class="text-red-500">*</span></label>
                                                <select name="verifikator[]" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent outline-none">
                                                    <option value="">-- Pilih Verifikator 1 --</option>
                                                    @foreach($verifikator1 as $v)
                                                    <option value="{{ $v->id }}">{{ $v->nama }} ({{ $v->grupVerifikasi->first()->nama_grup ?? '' }})</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-800 mb-1">Verifikator 2 (Opsional)</label>
                                                <select name="verifikator[]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent outline-none">
                                                    <option value="">-- Pilih Verifikator 2 --</option>
                                                    @foreach($verifikator2 as $v)
                                                    <option value="{{ $v->id }}">{{ $v->nama }} ({{ $v->grupVerifikasi->first()->nama_grup ?? '' }})</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-800 mb-1">Verifikator 3 (Opsional)</label>
                                                <select name="verifikator[]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent outline-none">
                                                    <option value="">-- Pilih Verifikator 3 --</option>
                                                    @foreach($verifikator3 as $v)
                                                    <option value="{{ $v->id }}">{{ $v->nama }} ({{ $v->grupVerifikasi->first()->nama_grup ?? '' }})</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                                <button type="submit" class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 sm:ml-3 sm:w-auto sm:text-sm">
                                                    Kirim Dokumen
                                                </button>
                                                <button @click="showVerifikatorModal = false" type="button" class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">
                                                    Batal
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                @if($isProposer)
                    @if($pengajuan->status === 'Ditolak Admin')
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
            @elseif($pengajuan->status === 'Menunggu Verifikasi')
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
            @elseif($pengajuan->status === 'Revisi')
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

        @if($pengajuan->catatan && in_array($pengajuan->status, ['Ditolak Admin', 'Revisi']))
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

<script src="{{ env('ONLYOFFICE_URL', 'http://localhost:8080') }}/web-apps/apps/api/documents/api.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const config = @js($config);
        const docEditor = new DocsAPI.DocEditor("placeholder", config);
    });
</script>
@endsection
