@extends('layouts.app')

@section('title', 'Dokumen Terbit — KERNAS')

@section('content')
<div class="max-w-screen-xl mx-auto px-4 py-8" x-data="dokumenTerbit(@js($dokumen))">
    <div class="mb-7 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Dokumen Terbit</h1>
            <p class="text-sm text-gray-500 mt-1">Daftar dokumen resmi yang telah diterbitkan (PUBLISHED).</p>
        </div>
        @if(session('pengguna.is_admin'))
        <div>
            <button @click="showUploadModal = true" class="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                Tambah Dokumen
            </button>
        </div>
        @endif
    </div>

    @if ($errors->any())
        <script>
            window.addEventListener('DOMContentLoaded', () => {
                Swal.fire({
                    title: 'Validasi Gagal!',
                    html: `
                        <ul style="text-align: left;">
                            @foreach ($errors->all() as $error)
                                <li>- {{ $error }}</li>
                            @endforeach
                        </ul>
                    `,
                    icon: 'error'
                });
            });
        </script>
    @endif
    
    @if (session('error'))
        <script>
            window.addEventListener('DOMContentLoaded', () => {
                Swal.fire('Gagal!', "{{ session('error') }}", 'error');
            });
        </script>
    @endif
    
    @if (session('success'))
        <script>
            window.addEventListener('DOMContentLoaded', () => {
                Swal.fire('Berhasil!', "{{ session('success') }}", 'success');
            });
        </script>
    @endif

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6">
        <div class="px-5 py-4 border-b border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4 bg-gray-50/50">
            <h2 class="text-sm font-bold text-gray-700">Daftar Dokumen</h2>
            
            <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
                {{-- Input Pencarian Nama --}}
                <div class="relative w-full sm:w-48">
                    <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                    </svg>
                    <input x-model="searchJudul" type="text" placeholder="Cari judul/nomor..." class="w-full pl-8 pr-3 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 outline-none">
                </div>
                
                {{-- Filter Tipe --}}
                <select x-model="filterTipe" class="w-full sm:w-auto px-3 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 outline-none text-gray-600 bg-white">
                    <option value="">Semua Tipe</option>
                    <option value="Naskah Dinas Arahan">Naskah Dinas Arahan</option>
                    <option value="Naskah Dinas Korespondensi">Naskah Dinas Korespondensi</option>
                    <option value="Naskah Dinas Khusus">Naskah Dinas Khusus</option>
                </select>
                
                {{-- Filter Tanggal --}}
                <input x-model="filterTanggal" type="date" class="w-full sm:w-auto px-3 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 outline-none text-gray-600">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 border-b border-gray-200 text-gray-600">
                    <tr>
                        <th class="px-5 py-3 font-semibold">Nomor Dokumen</th>
                        <th class="px-5 py-3 font-semibold">Judul Dokumen</th>
                        <th class="px-5 py-3 font-semibold">Pengusul</th>
                        <th class="px-5 py-3 font-semibold">Unit</th>
                        <th class="px-5 py-3 font-semibold w-24">Jenis</th>
                        <th class="px-5 py-3 font-semibold w-32 text-center">Tgl Terbit</th>
                        <th class="px-5 py-3 font-semibold w-32 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <template x-for="item in filteredData" :key="item.id">
                        <tr class="transition-colors border-b border-gray-50 hover:bg-gray-50">
                            <td class="px-5 py-3.5">
                                <p class="font-medium text-gray-800" x-text="item.nomor"></p>
                            </td>
                            <td class="px-5 py-3.5">
                                <p class="font-medium text-gray-800" x-text="item.judul"></p>
                            </td>
                            <td class="px-5 py-3.5">
                                <p class="font-medium text-gray-800" x-text="item.nama_pengusul"></p>
                            </td>
                            <td class="px-5 py-3.5">
                                <p class="font-medium text-gray-800 max-w-[150px] truncate" :title="item.unit_pengusul" x-text="item.unit_pengusul"></p>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="px-2 py-0.5 rounded text-xs font-bold bg-emerald-100 text-emerald-700" x-text="item.tipe"></span>
                            </td>
                            <td class="px-5 py-3.5 text-center text-gray-500">
                                <span x-text="formatDate(item.updated_at)"></span>
                            </td>
                            <td class="px-5 py-3.5 text-center space-x-2 whitespace-nowrap">
                                <a :href="'/dokumen/' + item.id + '/lihat'" target="_blank" class="inline-flex items-center justify-center w-8 h-8 rounded bg-gray-100 hover:bg-sky-100 text-gray-600 hover:text-sky-600 transition-colors tooltip" title="Lihat PDF">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </a>
                                <a :href="'/dokumen/' + item.id + '/unduh'" class="inline-flex items-center justify-center w-8 h-8 rounded bg-gray-100 hover:bg-emerald-100 text-gray-600 hover:text-emerald-600 transition-colors tooltip" title="Unduh PDF">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                                    </svg>
                                </a>
                                @if(session('pengguna.is_admin'))
                                <form :action="'/dokumen-terbit/' + item.id" method="POST" class="inline" @submit.prevent="appConfirm('Yakin ingin menghapus dokumen ini secara permanen? File dan data tidak dapat dikembalikan.', true).then(result => { if (result.isConfirmed) $event.target.submit(); })">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded bg-gray-100 hover:bg-red-100 text-gray-600 hover:text-red-600 transition-colors tooltip" title="Hapus Permanen">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                        </svg>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                    </template>
                    <tr x-show="filteredData.length === 0">
                        <td colspan="7" class="px-5 py-8 text-center text-gray-500">
                            Tidak ada dokumen yang ditemukan.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="px-5 py-4 border-t border-gray-100 bg-gray-50 flex items-center justify-between">
            <p class="text-xs text-gray-500">Menampilkan <span class="font-bold text-gray-700" x-text="filteredData.length"></span> dokumen</p>
        </div>
    </div>

    {{-- Modal Tambah Dokumen Langsung --}}
    @if(session('pengguna.is_admin'))
    <div x-show="showUploadModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showUploadModal" class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-900 opacity-50"></div>
            </div>
            <div x-show="showUploadModal" class="inline-block w-full max-w-2xl px-6 py-6 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-xl sm:my-8 sm:align-middle" @click.away="showUploadModal = false">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-bold text-gray-900">Upload Dokumen Langsung</h3>
                    <button @click="showUploadModal = false" class="text-gray-400 hover:text-gray-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <form action="/dokumen-terbit/langsung" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">File Dokumen (PDF)</label>
                            <input type="file" name="file_pdf" accept=".pdf" required class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100 border border-gray-300 rounded-lg p-1.5 focus:ring-2 focus:ring-sky-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Judul Dokumen</label>
                            <input type="text" name="nama_dokumen" required placeholder="Contoh: SK Rektor 2026..." class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 outline-none">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Surat</label>
                                <input type="text" name="nomor_surat" required placeholder="Contoh: 123/SK/2026" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Surat</label>
                                <select name="tipe_surat" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 outline-none">
                                    <option value="Naskah Dinas Arahan">Naskah Dinas Arahan</option>
                                    <option value="Naskah Dinas Korespondensi">Naskah Dinas Korespondensi</option>
                                    <option value="Naskah Dinas Khusus">Naskah Dinas Khusus</option>
                                </select>
                            </div>
                        </div>
                        <div x-data="{ searchUser: '' }">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Pengguna yang Dapat Melihat (Hak Akses)</label>
                            
                            {{-- Fitur Pencarian Mirip Select2 --}}
                            <div class="relative mb-2">
                                <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                                <input x-model="searchUser" type="text" placeholder="Ketik nama atau NIP untuk mencari..." class="w-full pl-8 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 outline-none">
                            </div>

                            <div class="max-h-48 overflow-y-auto border border-gray-200 rounded-lg p-3 space-y-2 bg-gray-50">
                                @foreach($semuaPengguna as $p)
                                    @if(!$p->is_admin)
                                        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer"
                                               x-show="'{{ strtolower(addslashes($p->nama . ' ' . $p->nip)) }}'.includes(searchUser.toLowerCase())">
                                            <input type="checkbox" name="pelihat[]" value="{{ $p->id }}" class="rounded text-sky-600 focus:ring-sky-500">
                                            {{ $p->nama }} <span class="text-xs text-gray-400">({{ $p->nip }})</span>
                                        </label>
                                    @endif
                                @endforeach
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Admin secara otomatis dapat melihat semua dokumen.</p>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" @click="showUploadModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Batal</button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-sky-600 rounded-lg hover:bg-sky-700">Upload Dokumen</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('dokumenTerbit', (initialData) => ({
        data: initialData,
        showUploadModal: false,
        searchJudul: '',
        filterTanggal: '',
        filterTipe: '',
        
        get filteredData() {
            return this.data.filter(item => {
                const searchLower = this.searchJudul.toLowerCase();
                const matchJudul = item.judul.toLowerCase().includes(searchLower) || (item.nomor && item.nomor.toLowerCase().includes(searchLower));
                
                let matchTanggal = true;
                if (this.filterTanggal) {
                    const itemDate = new Date(item.updated_at).toISOString().split('T')[0];
                    matchTanggal = itemDate === this.filterTanggal;
                }
                
                const matchTipe = this.filterTipe === '' || item.tipe === this.filterTipe;
                
                return matchJudul && matchTanggal && matchTipe;
            });
        },
        
        formatDate(dateStr) {
            if (!dateStr) return '-';
            const date = new Date(dateStr);
            return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
        }
    }));
});
</script>
@endsection
