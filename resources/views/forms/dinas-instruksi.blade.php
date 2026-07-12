@extends('layouts.app')

@section('title', 'Form Surat Instruksi — KERNAS')

@section('content')
<div class="max-w-screen-xl mx-auto px-4 py-8" x-data="instruksiForm(@js($penggunas))">
    <div class="mb-6 flex items-center gap-4">
        <a href="/dashboard" class="w-10 h-10 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50 hover:text-gray-900 transition-colors shadow-sm" title="Kembali ke Beranda">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Form: {{ $template->nama_template }}</h1>
            <p class="text-sm text-gray-500 mt-1">Lengkapi informasi di bawah ini untuk menggenerate draft surat.</p>
        </div>
    </div>

    @if(session('error'))
    <div class="bg-red-50 text-red-700 p-4 rounded-xl mb-6 flex items-center gap-3">
        <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <span class="text-sm">{{ session('error') }}</span>
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <form action="/pengajuan/form/dinas-instruksi" method="POST" enctype="multipart/form-data" class="p-6 space-y-8">
            @csrf
            
            <input type="hidden" name="id_template" value="{{ $template->id }}">

            <!-- Metadata Surat -->
            <div class="space-y-4">
                <h3 class="text-sm font-bold text-sky-700 uppercase tracking-wider border-b pb-2">1. Metadata Surat</h3>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-800 mb-2">Judul Dokumen (Sistem) <span class="text-red-500">*</span></label>
                    <p class="text-xs text-gray-500 mb-2">Nama dokumen ini di sistem (contoh: Instruksi Penggunaan Lab 2026)</p>
                    <input type="text" name="judul" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent outline-none">
                </div>
            </div>

            <!-- Informasi Surat -->
            <div class="space-y-4">
                <h3 class="text-sm font-bold text-sky-700 uppercase tracking-wider border-b pb-2">2. Informasi Kepala Surat</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-2">Nomor Surat</label>
                        <input type="text" name="NOMOR_SURAT" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent outline-none" placeholder="123/UN.1/2026">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-2">Tanggal Surat <span class="text-red-500">*</span></label>
                        <input type="date" name="TANGGAL_SURAT" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-800 mb-2">Judul Surat (TENTANG) <span class="text-red-500">*</span></label>
                    <input type="text" name="JUDUL_SURAT" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent outline-none" placeholder="PENGGUNAAN LABORATORIUM">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-800 mb-2">Dalam Rangka <span class="text-red-500">*</span></label>
                    <input type="text" name="RANGKA_SURAT" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent outline-none" placeholder="persiapan perkuliahan semester ganjil">
                </div>
            </div>

            <!-- Identitas Personal -->
            <div class="space-y-4">
                <h3 class="text-sm font-bold text-sky-700 uppercase tracking-wider border-b pb-2">3. Daftar Penerima Instruksi</h3>
                
                <div class="pt-2">
                    <label class="block text-sm font-semibold text-gray-800 mb-2">Daftar Anggota / Penerima <span class="text-red-500">*</span></label>
                    <p class="text-xs text-gray-500 mb-3">Tabel di dalam dokumen akan secara otomatis menyesuaikan jumlah orang yang Anda tambahkan di sini.</p>
                    
                    <div class="space-y-3">
                        <template x-for="(orang, index) in daftarAnggota" :key="index">
                            <div class="flex items-start gap-4 p-4 border border-gray-200 rounded-xl bg-gray-50 relative group transition-colors hover:border-sky-300">
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 flex-1">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Pilih Pegawai <span class="text-red-500">*</span></label>
                                        <div x-data="{ open: false, search: '' }" class="relative" @click.away="open = false">
                                            <div @click="open = !open" class="w-full px-3 py-1.5 border border-gray-300 rounded-md bg-white flex justify-between items-center cursor-pointer focus:ring-2 focus:ring-sky-500">
                                                <span x-text="orang.nama ? orang.nama : '-- Pilih Pegawai --'" class="text-sm text-gray-700 truncate"></span>
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                            </div>
                                            
                                            <div x-show="open" x-cloak class="absolute z-20 w-full mt-1 bg-white border border-gray-200 rounded-md shadow-lg">
                                                <div class="p-2 border-b border-gray-100">
                                                    <input type="text" x-model="search" placeholder="Cari nama..." class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-sky-500">
                                                </div>
                                                <ul class="max-h-48 overflow-y-auto">
                                                    <template x-for="p in penggunas.filter(x => x.nama.toLowerCase().includes(search.toLowerCase()))" :key="p.id">
                                                        <li @click="orang.id_pengguna = p.id; onPegawaiChange(index); open = false; search = ''" 
                                                            class="px-3 py-2 text-sm hover:bg-sky-50 cursor-pointer text-gray-700" 
                                                            x-text="p.nama"></li>
                                                    </template>
                                                    <li x-show="penggunas.filter(x => x.nama.toLowerCase().includes(search.toLowerCase())).length === 0" class="px-3 py-2 text-sm text-gray-500 text-center">Tidak ditemukan</li>
                                                </ul>
                                            </div>
                                            <input type="hidden" :name="'anggota['+index+'][id_pengguna]'" :value="orang.id_pengguna" required>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Nama</label>
                                        <input type="text" :name="'anggota['+index+'][nama]'" x-model="orang.nama" readonly class="w-full px-3 py-1.5 border border-gray-300 rounded-md bg-gray-100 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">NIP</label>
                                        <input type="text" :name="'anggota['+index+'][nip]'" x-model="orang.nip" readonly class="w-full px-3 py-1.5 border border-gray-300 rounded-md bg-gray-100 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Jabatan</label>
                                        <input type="text" :name="'anggota['+index+'][jabatan]'" x-model="orang.jabatan" readonly class="w-full px-3 py-1.5 border border-gray-300 rounded-md bg-gray-100 text-sm">
                                    </div>
                                </div>
                                <button type="button" @click="hapusAnggota(index)" x-show="daftarAnggota.length > 1" class="mt-6 p-2 text-red-500 hover:bg-red-100 rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                </button>
                            </div>
                        </template>
                    </div>

                    <button type="button" @click="tambahAnggota()" class="mt-4 flex items-center gap-2 px-4 py-2 text-sm font-semibold text-sky-600 bg-sky-50 hover:bg-sky-100 rounded-lg border border-sky-200 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        Tambah Penerima
                    </button>
                </div>
            </div>

            <!-- Daftar Instruksi -->
            <div class="space-y-4">
                <h3 class="text-sm font-bold text-sky-700 uppercase tracking-wider border-b pb-2">4. Daftar Instruksi</h3>
                
                <div class="pt-2">
                    <label class="block text-sm font-semibold text-gray-800 mb-2">Rincian Instruksi <span class="text-red-500">*</span></label>
                    <p class="text-xs text-gray-500 mb-3">Sistem akan secara otomatis memberikan penomoran (KESATU, KEDUA, KETIGA, dst) pada dokumen hasil berdasarkan urutan di bawah ini.</p>
                    
                    <div class="space-y-3">
                        <template x-for="(instruksi, index) in daftarInstruksi" :key="index">
                            <div class="flex items-start gap-3">
                                <div class="w-24 shrink-0 flex items-center h-10 px-3 bg-gray-100 border border-gray-300 rounded-lg text-sm font-bold text-gray-600" x-text="getUrutanText(index) + ':'">
                                </div>
                                <textarea :name="'instruksi['+index+']'" x-model="daftarInstruksi[index]" required rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent outline-none" placeholder="Isi instruksi..."></textarea>
                                
                                <button type="button" x-show="daftarInstruksi.length > 1" @click="hapusInstruksi(index)" class="h-10 px-3 py-2 bg-red-50 text-red-500 hover:bg-red-100 rounded-lg transition-colors flex-shrink-0 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                        </template>
                    </div>

                    <button type="button" @click="tambahInstruksi()" class="mt-4 flex items-center gap-2 px-4 py-2 text-sm font-semibold text-emerald-600 bg-emerald-50 hover:bg-emerald-100 rounded-lg border border-emerald-200 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        Tambah Instruksi
                    </button>
                </div>
            </div>

            <!-- Lampiran Opsional -->
            <div class="space-y-4">
                <h3 class="text-sm font-bold text-sky-700 uppercase tracking-wider border-b pb-2">5. Lampiran Pendukung</h3>
                
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
                            } else {
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

            <!-- Submit Button -->
            <div class="border-t border-gray-100 pt-6 flex justify-end">
                <button type="submit" class="px-6 py-2.5 text-sm font-bold text-white bg-sky-600 rounded-xl hover:bg-sky-700 transition-colors shadow-sm flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                    Simpan dan Lanjut Edit Dokumen
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function instruksiForm(penggunasData) {
        return {
            penggunas: penggunasData,
            
            daftarAnggota: [
                { id_pengguna: '', nama: '', nip: '', jabatan: '' }
            ],

            daftarInstruksi: [
                ''
            ],

            tambahAnggota() {
                this.daftarAnggota.push({ id_pengguna: '', nama: '', nip: '', jabatan: '' });
            },

            hapusAnggota(index) {
                if(this.daftarAnggota.length > 1) {
                    this.daftarAnggota.splice(index, 1);
                }
            },

            onPegawaiChange(index) {
                const id = this.daftarAnggota[index].id_pengguna;
                const p = this.penggunas.find(x => x.id == id);
                if(p) {
                    this.daftarAnggota[index].nama = p.nama;
                    this.daftarAnggota[index].nip = p.nip;
                    this.daftarAnggota[index].jabatan = p.jabatan || '';
                } else {
                    this.daftarAnggota[index].nama = '';
                    this.daftarAnggota[index].nip = '';
                    this.daftarAnggota[index].jabatan = '';
                }
            },

            tambahInstruksi() {
                this.daftarInstruksi.push('');
            },

            hapusInstruksi(index) {
                if (this.daftarInstruksi.length > 1) {
                    this.daftarInstruksi.splice(index, 1);
                }
            },

            getUrutanText(index) {
                const urutan = ["KESATU", "KEDUA", "KETIGA", "KEEMPAT", "KELIMA", "KEENAM", "KETUJUH", "KEDELAPAN", "KESEMBILAN", "KESEPULUH"];
                if (index < urutan.length) {
                    return urutan[index];
                }
                return "KE-" + (index + 1);
            }
        }
    }
</script>
@endsection
