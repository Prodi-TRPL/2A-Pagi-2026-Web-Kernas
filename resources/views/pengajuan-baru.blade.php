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

    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <form action="/pengajuan/baru" method="POST" class="p-6 space-y-6">
            @csrf

            <div>
                <label class="block text-sm font-semibold text-gray-800 mb-2">Pilih Template Surat <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($templates as $tmpl)
                    <label class="relative flex cursor-pointer rounded-xl border border-gray-200 bg-white p-4 shadow-sm focus:outline-none hover:border-sky-300 hover:bg-sky-50 transition-colors has-[:checked]:border-sky-500 has-[:checked]:ring-1 has-[:checked]:ring-sky-500">
                        <input type="radio" name="id_template" value="{{ $tmpl->id }}" class="sr-only" required>
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-bold text-gray-900">{{ $tmpl->nama }}</p>
                                <p class="text-xs text-gray-500">{{ $tmpl->tipe }}</p>
                            </div>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="border-t border-gray-100 pt-6">
                <label class="block text-sm font-semibold text-gray-800 mb-2">Judul Surat <span class="text-red-500">*</span></label>
                <input type="text" name="judul" required placeholder="Contoh: SK Pengangkatan Pegawai Baru 2026" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent outline-none">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-800 mb-2">Daftar Menimbang <span class="text-red-500">*</span></label>
                    <textarea name="daftar_menimbang" required rows="4" placeholder="1. Bahwa...&#10;2. Bahwa..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent outline-none text-sm"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-800 mb-2">Daftar Memperhatikan <span class="text-red-500">*</span></label>
                    <textarea name="daftar_memperhatikan" required rows="4" placeholder="1. Undang-undang...&#10;2. Peraturan..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent outline-none text-sm"></textarea>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-800 mb-2">Daftar Memutuskan <span class="text-red-500">*</span></label>
                <textarea name="daftar_memutuskan" required rows="4" placeholder="MENETAPKAN: ...&#10;KESATU: ...&#10;KEDUA: ..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent outline-none text-sm"></textarea>
            </div>

            <div class="border-t border-gray-100 pt-6">
                <label class="block text-sm font-semibold text-gray-800 mb-2">Grup Verifikasi Tujuan <span class="text-red-500">*</span></label>
                <p class="text-xs text-gray-500 mb-3">Pilih jalur verifikasi (berjenjang) yang harus dilalui oleh pengajuan ini sebelum diterbitkan.</p>
                <select name="id_grup_verifikasi" required class="w-full max-w-md px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent outline-none">
                    <option value="">-- Pilih Grup Verifikasi --</option>
                    @foreach($grupVerifikasi as $grup)
                    <option value="{{ $grup->id }}">{{ $grup->nama_grup }}</option>
                    @endforeach
                </select>
            </div>

            <div class="border-t border-gray-100 pt-6 flex justify-end">
                <button type="submit" class="px-6 py-2.5 text-sm font-bold text-white bg-sky-600 rounded-xl hover:bg-sky-700 transition-colors shadow-sm flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                    Proses dan Buka Editor
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
