@extends('layouts.app')

@section('title', 'Buat Pengajuan Baru — KERNAS')

@section('content')
<div class="max-w-screen-xl mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Buat Pengajuan Baru</h1>
        <p class="text-sm text-gray-500 mt-1">Pilih template dan isi informasi dasar surat Anda.</p>
    </div>

    @if(session('error'))
    <div class="bg-red-50 text-red-700 p-4 rounded-xl mb-6 flex items-center gap-3">
        <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <span class="text-sm">{{ session('error') }}</span>
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-sm font-semibold text-gray-800 mb-4">Daftar Template Surat</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($templates as $tmpl)
            @php
                $formUrl = '#';
                if (stripos($tmpl->filepath, 'contoh_surat_satu') !== false || stripos($tmpl->nama_template, 'SK') !== false) {
                    $formUrl = '/pengajuan/form/contoh-surat-satu';
                } elseif (stripos($tmpl->tipe, 'Arahan') !== false || stripos($tmpl->nama_template, 'Arahan') !== false) {
                    $formUrl = '/pengajuan/form/dinas-arahan';
                }
            @endphp
            <a href="{{ $formUrl }}" class="relative flex items-start gap-4 rounded-xl border border-gray-200 bg-white p-4 shadow-sm hover:border-sky-300 hover:bg-sky-50 transition-colors group">
                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 flex-shrink-0 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-bold text-gray-900 group-hover:text-sky-700 transition-colors">{{ $tmpl->nama_template }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $tmpl->tipe }}</p>
                    
                    <div class="mt-3 inline-flex items-center gap-1 text-xs font-semibold text-sky-600">
                        Isi Surat 
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
        
        @if($templates->isEmpty())
        <div class="text-center py-8 text-gray-500 text-sm">
            Belum ada template surat yang aktif. Silakan hubungi admin.
        </div>
        @endif
    </div>
</div>
@endsection
