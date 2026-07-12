@extends('layouts.app')

@section('title', 'Form Surat Rekomendasi — KERNAS')

@section('content')
<div class="max-w-screen-xl mx-auto px-4 py-8">
    <div class="mb-6 flex items-center gap-4">
        <a href="/dashboard" class="w-10 h-10 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50 hover:text-gray-900 transition-colors shadow-sm" title="Kembali ke Beranda">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Form: {{ $template->nama_template }}</h1>
            <p class="text-sm text-gray-500 mt-1">Lengkapi informasi di bawah ini untuk menggenerate draft dokumen.</p>
        </div>
    </div>

    @if(session('error'))
    <div class="bg-red-50 text-red-700 p-4 rounded-xl mb-6 flex items-center gap-3">
        <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <span class="text-sm">{{ session('error') }}</span>
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <form action="/pengajuan/form/dinas-khusus-rekomendasi" method="POST" enctype="multipart/form-data" class="p-6 space-y-8">
            @csrf
            <input type="hidden" name="id_template" value="{{ $template->id }}">

            <!-- Metadata & Isi Surat -->
            <div class="space-y-4">
                <h3 class="text-sm font-bold text-sky-700 uppercase tracking-wider border-b pb-2">1. Metadata & Isi Surat</h3>
                
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-2">Judul Surat <span class="text-red-500">*</span></label>
                        <input type="text" name="judul" required placeholder="Contoh: Surat Rekomendasi Beasiswa" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent outline-none text-sm transition-shadow">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-2">Nama Pemberi Rekomendasi <span class="text-red-500">*</span></label>
                        <input type="text" name="NAMA_PENGUSULAN" required placeholder="" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent outline-none text-sm transition-shadow">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-2">NIP Pemberi Rekomendasi <span class="text-red-500">*</span></label>
                        <input type="text" name="NIP_PENGUSULAN" required placeholder="" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent outline-none text-sm transition-shadow">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-2">Pangkat Pemberi Rekomendasi <span class="text-red-500">*</span></label>
                        <input type="text" name="PANGKAT_PENGUSULAN" required placeholder="" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent outline-none text-sm transition-shadow">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-2">Golongan Pemberi Rekomendasi <span class="text-red-500">*</span></label>
                        <input type="text" name="GOLONGAN_PENGUSULAN" required placeholder="" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent outline-none text-sm transition-shadow">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-2">Jabatan Pemberi Rekomendasi <span class="text-red-500">*</span></label>
                        <input type="text" name="JABATAN_PENGUSULAN" required placeholder="" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent outline-none text-sm transition-shadow">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-2">Alamat Pemberi Rekomendasi <span class="text-red-500">*</span></label>
                        <textarea name="ALAMAT_PENGUSULAN" rows="3" required placeholder="" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent outline-none text-sm transition-shadow"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-2">Nama Penerima Rekomendasi <span class="text-red-500">*</span></label>
                        <input type="text" name="NAMA_DIUSULIN" required placeholder="" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent outline-none text-sm transition-shadow">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-2">NIP Penerima Rekomendasi <span class="text-red-500">*</span></label>
                        <input type="text" name="NIP_DIUSULIN" required placeholder="" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent outline-none text-sm transition-shadow">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-2">Pangkat Penerima Rekomendasi <span class="text-red-500">*</span></label>
                        <input type="text" name="PANGKAT_DIUSULIN" required placeholder="" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent outline-none text-sm transition-shadow">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-2">Golongan Penerima Rekomendasi <span class="text-red-500">*</span></label>
                        <input type="text" name="GOLONGAN_DIUSULIN" required placeholder="" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent outline-none text-sm transition-shadow">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-2">Jabatan Penerima Rekomendasi <span class="text-red-500">*</span></label>
                        <input type="text" name="JABATAN_DIUSULIN" required placeholder="" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent outline-none text-sm transition-shadow">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-2">Tujuan Rekomendasi <span class="text-red-500">*</span></label>
                        <textarea name="REKOMENDASI_UNTUK_APA" rows="3" required placeholder="Rekomendasi ini diberikan untuk..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent outline-none text-sm transition-shadow"></textarea>
                    </div>
                </div>
            </div>



            <!-- lampiran (opsional) -->
            <div class="space-y-4">
                <h3 class="text-sm font-bold text-sky-700 uppercase tracking-wider border-b pb-2">Lampiran Pendukung</h3>
                
                <div x-data="{ 
                    files: [], 
                    isDropping: false,
                    addFiles(fileList) {
                        let dt = new DataTransfer();
                        this.files.forEach(f => dt.items.add(f));
                        let rejected = [];
                        Array.from(fileList).forEach(f => {
                            if (f.type !== 'application/pdf') {
                                rejected.push(f.name);
                                return;
                            }
                            if (!this.files.some(existing => existing.name === f.name)) {
                                dt.items.add(f);
                            }
                        });
                        this.files = Array.from(dt.files);
                        this.$refs.fileInput.files = dt.files;
                        if (rejected.length > 0) {
                            alert('File berikut ditolak karena bukan PDF:\n' + rejected.join('\n'));
                        }
                    },
                    removeFile(index) {
                        let dt = new DataTransfer();
                        this.files.splice(index, 1);
                        this.files.forEach(f => dt.items.add(f));
                        this.files = Array.from(dt.files);
                        this.$refs.fileInput.files = dt.files;
                    }
                }" class="w-full">
                    <div @dragover.prevent="isDropping = true"
                         @dragleave.prevent="isDropping = false"
                         @drop.prevent="isDropping = false; addFiles($event.dataTransfer.files)"
                         :class="isDropping ? 'border-sky-500 bg-sky-50' : 'border-gray-300 hover:border-sky-400'"
                         class="border-2 border-dashed rounded-xl p-8 text-center transition-colors cursor-pointer"
                         @click="$refs.fileInput.click()">
                        
                        <input x-ref="fileInput" type="file" name="file_lampiran[]" multiple accept="application/pdf" @change="addFiles($event.target.files)" class="hidden">
                        
                        <div class="flex flex-col items-center justify-center pointer-events-none">
                            <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
                            </svg>
                            <p class="text-sm font-medium text-gray-800">Unggah File Lampiran PDF (Opsional)</p>
                            <p class="text-xs text-gray-500 mt-1">Tarik & lepas file di sini, atau <span class="text-sky-600 font-semibold">klik untuk mencari</span></p>
                            <p class="text-[10px] text-gray-400 mt-2">Hanya file <strong>PDF</strong> yang diterima, maksimal 5 MB per file.</p>
                        </div>
                    </div>
                    
                    <template x-if="files.length > 0">
                        <div class="mt-4 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                            <p class="text-xs font-semibold text-gray-600 mb-3 flex items-center justify-between">
                                <span>File yang dipilih (<span x-text="files.length"></span>)</span>
                                <button type="button" @click.stop="files = []; $refs.fileInput.value = ''" class="text-red-500 hover:text-red-700 underline text-[10px]">Hapus Semua</button>
                            </p>
                            <ul class="space-y-2">
                                <template x-for="(file, index) in files" :key="index">
                                    <li class="text-xs text-gray-700 flex items-center gap-2 bg-white p-2 rounded border border-gray-100">
                                        <svg class="w-4 h-4 text-sky-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                        <span x-text="file.name" class="truncate font-medium flex-1"></span>
                                        <span class="text-gray-400 shrink-0" x-text="'(' + (file.size/1024).toFixed(0) + ' KB)'"></span>
                                        <button type="button" @click.stop="removeFile(index)" class="text-gray-400 hover:text-red-500 p-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </template>
                </div>
            </div>

            <div class="border-t border-gray-100 pt-6 flex justify-end">
                <button type="submit" class="px-6 py-2.5 text-sm font-bold text-white bg-sky-600 rounded-xl hover:bg-sky-700 transition-colors shadow-sm flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                    Proses Draft Surat
                </button>
            </div>
        </form>
    </div>
</div>
@endsection