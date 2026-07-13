@extends('layouts.app')

@section('title', 'Daftar Pengajuan Surat Saya — KERNAS')

@section('content')
<div class="max-w-screen-xl mx-auto px-4 py-8" x-data="pengajuanSurat(@js($pengajuans))">
    <div class="mb-7 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Pengajuan Surat Saya</h1>
            <p class="text-sm text-gray-500 mt-1">Daftar semua usulan dokumen yang telah Anda buat.</p>
        </div>
    </div>

    {{-- Dropdown Pilihan Pembuatan Surat (seperti di Dashboard) --}}
        {{-- Searchable Dropdown Pilihan Pembuatan Surat --}}
    <div class="mb-8 relative w-full" x-data="{
        open: false,
        search: '',
        items: [
            @php $allTemplates = collect($templatesArahan ?? [])->merge($templatesKorespondensi ?? [])->merge($templatesKhusus ?? []); @endphp
            @foreach($allTemplates as $tmpl)
                @php
                    $formUrl = '#';
                    if (stripos($tmpl->filepath, 'contoh_surat_satu') !== false || stripos($tmpl->nama_template, 'SK') !== false) {
                        $formUrl = '/pengajuan/form/contoh-surat-satu';
                    } elseif (stripos($tmpl->filepath, 'dinas_instruksi') !== false || stripos($tmpl->nama_template, 'Instruksi') !== false) {
                        $formUrl = '/pengajuan/form/dinas-instruksi';
                    } elseif (stripos($tmpl->filepath, 'edaran') !== false) {
                        $formUrl = '/pengajuan/form/arahan-edaran';
                    } elseif (stripos($tmpl->filepath, 'perintah') !== false) {
                        $formUrl = '/pengajuan/form/arahan-perintah';
                    } elseif (stripos($tmpl->filepath, 'keputusan') !== false) {
                        $formUrl = '/pengajuan/form/arahan-keputusan';
                    } elseif (stripos($tmpl->filepath, 'tugas_biasa') !== false) {
                        $formUrl = '/pengajuan/form/arahan-tugas-biasa';
                    } elseif (stripos($tmpl->filepath, 'tugas_tabel') !== false) {
                        $formUrl = '/pengajuan/form/arahan-tugas-tabel';
                    } elseif (stripos($tmpl->filepath, 'dinas_arahan_bentuk_peraturan') !== false || stripos($tmpl->nama_template, 'Arahan') !== false) {
                        $formUrl = '/pengajuan/form/dinas-arahan';
                    } elseif (stripos($tmpl->filepath, 'korespondensi_nota_dinas') !== false) {
                        $formUrl = '/pengajuan/form/korespondensi-nota';
                    } elseif (stripos($tmpl->filepath, 'korespondensi_surat_dinas') !== false) {
                        $formUrl = '/pengajuan/form/korespondensi-dinas';
                    } elseif (stripos($tmpl->filepath, 'korespondensi_surat_undangan') !== false) {
                        $formUrl = '/pengajuan/form/korespondensi-undangan';
                    } elseif (stripos($tmpl->filepath, 'dinas_khusus_surat_keterangan') !== false) {
                        $formUrl = '/pengajuan/form/dinas-khusus-keterangan?id=' . $tmpl->id;
                    } elseif (stripos($tmpl->filepath, 'dinas_khusus_surat_pengantar') !== false) {
                        $formUrl = '/pengajuan/form/dinas-khusus-pengantar?id=' . $tmpl->id;
                    } elseif (stripos($tmpl->filepath, 'dinas_khusus_surat_pengumuman') !== false) {
                        $formUrl = '/pengajuan/form/dinas-khusus-pengumuman?id=' . $tmpl->id;
                    } elseif (stripos($tmpl->filepath, 'dinas_khusus_surat_pernyataan') !== false) {
                        $formUrl = '/pengajuan/form/dinas-khusus-pernyataan?id=' . $tmpl->id;
                    } elseif (stripos($tmpl->filepath, 'dinas_khusus_surat_rekomendasi') !== false) {
                        $formUrl = '/pengajuan/form/dinas-khusus-rekomendasi?id=' . $tmpl->id;
                    }
                @endphp
                { 
                    id: {{ $tmpl->id }}, 
                    nama: '{{ addslashes($tmpl->nama_template) }}', 
                    tipe: '{{ addslashes($tmpl->tipe) }}', 
                    url: '{{ $formUrl }}',
                    badgeColor: '{{ stripos($tmpl->tipe, "Arahan") !== false ? "bg-sky-100 text-sky-700" : (stripos($tmpl->tipe, "Korespondensi") !== false ? "bg-emerald-100 text-emerald-700" : "bg-violet-100 text-violet-700") }}'
                },
            @endforeach
        ],
        get filteredItems() {
            if (this.search === '') return this.items;
            return this.items.filter(item => item.nama.toLowerCase().includes(this.search.toLowerCase()) || item.tipe.toLowerCase().includes(this.search.toLowerCase()));
        }
    }" @click.away="open = false">
        
        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Buat Pengajuan Naskah Dinas Baru</label>
        
        <div class="relative">
            <!-- Search Input -->
            <div class="relative flex items-center">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input 
                    type="text" 
                    x-model="search"
                    @focus="open = true"
                    @keydown.escape="open = false"
                    placeholder="Cari atau pilih jenis surat yang ingin Anda buat..."
                    class="w-full pl-11 pr-10 py-3.5 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-sky-500 focus:border-transparent outline-none text-gray-700 bg-white transition-all text-sm font-medium"
                >
                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
            </div>

            <!-- Dropdown List -->
            <div x-show="open" 
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="absolute z-50 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-xl max-h-[300px] overflow-y-auto"
                 style="display: none;">
                 
                <template x-for="item in filteredItems" :key="item.id">
                    <div @click="if(item.url !== '#') window.location.href = item.url"
                         class="px-5 py-3.5 cursor-pointer hover:bg-sky-50 transition-colors border-b border-gray-50 last:border-b-0 flex items-center justify-between">
                        <div class="font-semibold text-gray-800" x-text="item.nama"></div>
                        <span class="px-2.5 py-1 rounded-md text-[10px] font-bold tracking-wider uppercase ml-3 whitespace-nowrap"
                              :class="item.badgeColor"
                              x-text="item.tipe.replace('Naskah Dinas ', '')">
                        </span>
                    </div>
                </template>
                
                <div x-show="filteredItems.length === 0" class="px-5 py-8 text-sm text-gray-500 text-center flex flex-col items-center">
                    <svg class="w-10 h-10 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Tidak ada jenis surat yang cocok dengan kata kunci tersebut.</span>
                </div>
            </div>
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
                
                {{-- Filter Jenis --}}
                <select x-model="filterJenis" class="w-full sm:w-auto px-3 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 outline-none bg-white">
                    <option value="">Semua Jenis</option>
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
                        <th class="px-5 py-3 font-semibold">Judul Pengajuan</th>
                        <th class="px-5 py-3 font-semibold w-24">Jenis</th>
                        <th class="px-5 py-3 font-semibold w-32 text-center">Status</th>
                        <th class="px-5 py-3 font-semibold w-32 text-center">Tanggal</th>
                        <th class="px-5 py-3 font-semibold w-24 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <template x-for="item in filteredData" :key="item.id">
                        <tr @click="window.location.href = '/pengajuan/' + item.id + '/edit'" class="hover:bg-gray-50 transition-colors border-b border-gray-50 cursor-pointer">
                            <td class="px-5 py-3.5">
                                <p class="font-medium text-gray-800" x-text="item.judul"></p>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="px-2 py-0.5 rounded text-xs font-bold"
                                      :class="item.tipe === 'Naskah Dinas Arahan' ? 'bg-sky-100 text-sky-700' : (item.tipe === 'Naskah Dinas Korespondensi' ? 'bg-emerald-100 text-emerald-700' : 'bg-violet-100 text-violet-700')"
                                      x-text="item.tipe.replace('Naskah Dinas ', '')">
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold whitespace-nowrap inline-block"
                                      :class="{
                                          'bg-amber-100 text-amber-700': item.status == 'Diproses Admin',
                                          'bg-indigo-100 text-indigo-700': item.status == 'Menunggu Verifikasi',
                                          'bg-sky-100 text-sky-700': item.status == 'Diterbitkan',
                                          'bg-red-100 text-red-700': ['Ditolak Admin', 'Revisi'].includes(item.status),
                                          'bg-gray-100 text-gray-700': item.status == 'Draf'
                                      }" x-text="item.status"></span>
                            </td>
                            <td class="px-5 py-3.5 text-gray-500 text-xs text-center" x-text="item.tgl_masuk">
                            </td>
                            <td class="px-5 py-3.5 text-center" @click.stop>
                                <div class="flex items-center justify-center gap-2">
                                    <a :href="'/pengajuan/' + item.id + '/edit'"
                                       class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-sky-600 border border-sky-200 rounded-lg hover:bg-sky-50 transition-colors">
                                        Detail
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    </template>
                    
                    <tr x-show="filteredData.length === 0" x-cloak>
                        <td colspan="6" class="px-5 py-12 text-center text-gray-400">
                            <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                            </svg>
                            <p class="text-sm">Tidak ada pengajuan surat ditemukan.</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function pengajuanSurat(initialData) {
        return {
            items: initialData || [],
            searchJudul: '',
            filterJenis: '',
            filterTanggal: '',
            
            get filteredData() {
                return this.items.filter(item => {
                    const matchSearch = !this.searchJudul || item.judul.toLowerCase().includes(this.searchJudul.toLowerCase());
                    const matchJenis = !this.filterJenis || (item.tipe && item.tipe.includes(this.filterJenis));
                    const matchTanggal = !this.filterTanggal || item.raw_tgl_masuk === this.filterTanggal;
                    
                    return matchSearch && matchJenis && matchTanggal;
                });
            }
        };
    }
</script>
@endsection
