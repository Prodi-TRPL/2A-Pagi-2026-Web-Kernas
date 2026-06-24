@extends('layouts.app')

@section('title', 'Dokumen Terbit — KERNAS')

@section('content')
<div class="max-w-screen-xl mx-auto px-4 py-8" x-data="dokumenTerbit(@js($dokumen))">
    <div class="mb-7 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Dokumen Terbit</h1>
            <p class="text-sm text-gray-500 mt-1">Daftar dokumen resmi yang telah diterbitkan (PUBLISHED).</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6">
        <div class="px-5 py-4 border-b border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4 bg-gray-50/50">
            <h2 class="text-sm font-bold text-gray-700">Daftar Dokumen</h2>
            
            <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
                {{-- Input Pencarian Nama --}}
                <div class="relative w-full sm:w-48">
                    <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                    </svg>
                    <input x-model="searchJudul" type="text" placeholder="Cari judul dokumen..." class="w-full pl-8 pr-3 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 outline-none">
                </div>
                
                {{-- Filter Tanggal --}}
                <input x-model="filterTanggal" type="date" class="w-full sm:w-auto px-3 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 outline-none text-gray-600">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 border-b border-gray-200 text-gray-600">
                    <tr>
                        <th class="px-5 py-3 font-semibold">Judul Dokumen</th>
                        <th class="px-5 py-3 font-semibold w-24">Jenis</th>
                        <th class="px-5 py-3 font-semibold w-32 text-center">Tgl Terbit</th>
                        <th class="px-5 py-3 font-semibold w-24 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <template x-for="item in filteredData" :key="item.id">
                        <tr @click="if(item.pengajuan_id) window.location.href = '/pengajuan/' + item.pengajuan_id + '/edit'" :class="item.pengajuan_id ? 'cursor-pointer hover:bg-gray-50' : 'opacity-75'" class="transition-colors border-b border-gray-50">
                            <td class="px-5 py-3.5">
                                <p class="font-medium text-gray-800" x-text="item.judul"></p>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="px-2 py-0.5 rounded text-xs font-bold bg-emerald-100 text-emerald-700" x-text="item.tipe"></span>
                            </td>
                            <td class="px-5 py-3.5 text-center text-gray-500">
                                <span x-text="formatDate(item.updated_at)"></span>
                            </td>
                            <td class="px-5 py-3.5 text-center" @click.stop>
                                <template x-if="item.pengajuan_id">
                                    <a :href="'/pengajuan/' + item.pengajuan_id + '/edit'" class="inline-flex items-center justify-center w-8 h-8 rounded bg-gray-100 hover:bg-sky-100 text-gray-600 hover:text-sky-600 transition-colors tooltip" title="Lihat Dokumen">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    </a>
                                </template>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="filteredData.length === 0">
                        <td colspan="4" class="px-5 py-8 text-center text-gray-500">
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
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('dokumenTerbit', (initialData) => ({
        data: initialData,
        searchJudul: '',
        filterTanggal: '',
        
        get filteredData() {
            return this.data.filter(item => {
                const matchJudul = item.judul.toLowerCase().includes(this.searchJudul.toLowerCase());
                
                let matchTanggal = true;
                if (this.filterTanggal) {
                    const itemDate = new Date(item.updated_at).toISOString().split('T')[0];
                    matchTanggal = itemDate === this.filterTanggal;
                }
                
                return matchJudul && matchTanggal;
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
