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
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">POSTED</span>
            </div>
            <p class="text-sm text-gray-500 mt-1 ml-10">Judul: <span class="font-medium text-gray-700">{{ $pengajuan->judul }}</span></p>
        </div>
        
        <div class="flex items-center gap-3">
            <span class="text-xs text-gray-500 font-medium bg-gray-100 px-3 py-1.5 rounded-lg border border-gray-200">
                Penyimpanan otomatis saat mengetik
            </span>
            <button class="px-4 py-2 text-sm font-semibold text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                Selesai & Kirim ke Verifikator
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-50 text-green-700 px-6 py-3 border-b border-green-100 flex items-center gap-3 shrink-0">
        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span class="text-sm font-medium">{{ session('success') }}</span>
    </div>
    @endif

    <div class="flex-1 w-full bg-gray-200 relative">
        <div id="placeholder" class="absolute inset-0"></div>
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
