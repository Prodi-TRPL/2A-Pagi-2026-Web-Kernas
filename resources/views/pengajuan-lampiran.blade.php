@extends('layouts.app')

@section('title', 'Penampil Lampiran — KERNAS')

@section('content')
<div class="h-[calc(100vh-64px)] flex flex-col bg-gray-100" x-data="{
    lampirans: @js($lampirans),
    activeLampiran: {{ count($lampirans) > 0 ? 0 : 'null' }}
}">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between shrink-0 shadow-sm z-10">
        <div class="flex items-center gap-4">
            <a href="/pengajuan/{{ $pengajuan->id }}/edit" class="flex items-center gap-2 px-3 py-1.5 text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                Kembali ke Editor
            </a>
            <div class="h-6 w-px bg-gray-300"></div>
            <div>
                <h1 class="text-lg font-bold text-gray-900 leading-tight">Penampil Lampiran</h1>
                <p class="text-xs text-gray-500 font-medium">{{ $pengajuan->judul }}</p>
            </div>
        </div>
        
        <div class="flex items-center gap-3">
            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                Total: {{ count($lampirans) }} Lampiran
            </span>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex overflow-hidden">
        @if(count($lampirans) > 0)
            <!-- Viewer Kiri -->
            <div class="flex-1 bg-gray-200 p-4 overflow-hidden relative">
                <div class="w-full h-full bg-white rounded-xl shadow-sm border border-gray-300 overflow-hidden relative flex flex-col">
                    <!-- Toolbar dalam viewer -->
                    <div class="bg-gray-50 border-b border-gray-200 px-4 py-2 flex items-center justify-between shrink-0">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                            <span class="text-sm font-bold text-gray-700" x-text="lampirans[activeLampiran].nama_file"></span>
                        </div>
                        {{-- Link download melalui controller terproteksi, bukan akses /storage langsung --}}
                        <a :href="'/pengajuan/{{ $pengajuan->id }}/lampiran/' + lampirans[activeLampiran].id + '/unduh?download=1'" 
                           class="text-xs font-semibold text-sky-600 hover:text-sky-800 flex items-center gap-1">
                            Unduh
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        </a>
                    </div>
                    
                    <!-- Iframe Viewer — PDF ditampilkan via route terproteksi -->
                    <div class="flex-1 w-full bg-gray-100">
                        <iframe :src="'/pengajuan/{{ $pengajuan->id }}/lampiran/' + lampirans[activeLampiran].id + '/unduh'" class="w-full h-full border-0"></iframe>
                    </div>
                </div>
            </div>

            <!-- Sidebar Kanan -->
            <div class="w-80 bg-white border-l border-gray-200 flex flex-col shrink-0">
                @if($pengajuan->catatan && in_array($pengajuan->status, ['Ditolak Admin', 'Revisi']))
                <div class="p-4 border-b border-red-200 bg-red-50">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <h2 class="text-sm font-bold text-red-800 uppercase tracking-wider">Catatan Penolakan</h2>
                    </div>
                    <div class="max-h-32 overflow-y-auto">
                        <p class="text-xs text-red-700 bg-white p-2.5 rounded-lg border border-red-100 whitespace-pre-wrap leading-relaxed">{{ $pengajuan->catatan }}</p>
                    </div>
                </div>
                @endif
                
                <div class="p-4 border-b border-gray-100 bg-gray-50">
                    <h2 class="text-sm font-bold text-gray-800 uppercase tracking-wider">Daftar Lampiran</h2>
                    <p class="text-xs text-gray-500 mt-1">Pilih file untuk melihat isinya.</p>
                </div>
                
                <div class="flex-1 overflow-y-auto p-3 space-y-2">
                    <template x-for="(lampiran, index) in lampirans" :key="index">
                        <button @click="activeLampiran = index" 
                                class="w-full text-left p-3 rounded-lg border transition-all duration-200 group relative overflow-hidden"
                                :class="activeLampiran === index ? 'bg-sky-50 border-sky-300 shadow-sm' : 'bg-white border-gray-200 hover:border-sky-300 hover:bg-gray-50'">
                            
                            <!-- Indikator aktif -->
                            <div x-show="activeLampiran === index" class="absolute left-0 top-0 bottom-0 w-1 bg-sky-500"></div>
                            
                            <div class="flex items-start gap-3">
                                <div class="mt-0.5 p-1.5 rounded-md" :class="activeLampiran === index ? 'bg-sky-100 text-sky-600' : 'bg-gray-100 text-gray-500 group-hover:text-sky-500'">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold truncate transition-colors"
                                       :class="activeLampiran === index ? 'text-sky-900' : 'text-gray-700 group-hover:text-sky-700'"
                                       x-text="lampiran.nama_file"></p>
                                    <p class="text-xs text-gray-400 mt-1">Lampiran ke-<span x-text="index + 1"></span></p>
                                </div>
                            </div>
                        </button>
                    </template>
                </div>
            </div>
        @else
            <div class="flex-1 flex flex-col items-center justify-center p-8 text-center bg-gray-50">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-1">Tidak Ada Lampiran</h3>
                <p class="text-sm text-gray-500 max-w-sm">Surat ini tidak memiliki lampiran pendukung apa pun yang diunggah oleh pengusul.</p>
            </div>
        @endif
    </div>
</div>
@endsection
