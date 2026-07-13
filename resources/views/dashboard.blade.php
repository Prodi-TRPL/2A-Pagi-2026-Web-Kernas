@extends('layouts.app')

@section('title', 'Dashboard — KERNAS')

@section('content')
    {{-- (alur data: variabel $pengajuanterbaru, $dokumenterbaru, $chartdata, dan $stats yang ada di sini dikirim (diambil) dari fungsi index() di dashboardcontroller pada baris 107) --}}
    <div x-data="dashboard(@js($pengajuanTerbaru), @js($dokumenTerbaru), @js($chartData), @js($stats))">
        <div class="max-w-screen-xl mx-auto px-4 py-8">

        {{-- bagian yang digunakan untuk menampilkan judul dan header halaman --}}
        <div class="mb-7">
            <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
            <p class="text-sm text-gray-500 mt-1">Selamat datang, <span class="font-medium text-gray-700">{{ $user['nama'] ?? 'Admin' }}</span> — berikut ringkasan aktivitas hari ini</p>
        </div>

        @if (!$user['is_admin'] && !$user['is_verifikator'])
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
        @endif

        @if ($user['is_admin'])
        {{-- bagian pembungkus (wrapper) untuk menampilkan grafik dan tabel proses secara berdampingan --}}
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">

            {{-- bagian yang digunakan untuk menampilkan grafik aktivitas surat (mengambil 2/3 lebar layar) --}}
            <div class="xl:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="px-5 pt-5 pb-4 border-b border-gray-100">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <h2 class="text-base font-bold text-gray-900">Aktivitas Surat</h2>
                            <p class="text-xs text-gray-500 mt-0.5">Surat masuk (pengajuan) vs surat keluar (diterbitkan)</p>
                        </div>

                        {{-- bagian tombol untuk memilih rentang waktu/periode pada grafik --}}
                        <div class="flex items-center gap-1 bg-gray-100 rounded-lg p-1 text-xs font-medium">
                            <template x-for="p in periods" :key="p.key">
                                <button
                                    @click="setPeriod(p.key)"
                                    :class="activePeriod === p.key ? 'bg-white text-sky-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                                    class="px-3 py-1.5 rounded-md transition-all"
                                    x-text="p.label"
                                ></button>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- bagian yang digunakan untuk menampilkan keterangan warna (legend) dari grafik --}}
                <div class="px-5 pt-3 flex items-center gap-5">
                    <div class="flex items-center gap-1.5">
                        <div class="w-3 h-3 rounded-full bg-sky-500"></div>
                        <span class="text-xs text-gray-500">Surat Masuk</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                        <span class="text-xs text-gray-500">Surat Keluar</span>
                    </div>
                </div>

                <div class="p-5 pt-2">
                    <canvas id="suratChart" height="200"></canvas>
                </div>
            </div>

            {{-- bagian yang digunakan untuk menampilkan ringkasan statistik (mengambil 1/3 lebar layar) --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="px-5 pt-5 pb-4 border-b border-gray-100">
                    <h2 class="text-base font-bold text-gray-900">Ringkasan Periode</h2>
                    <p class="text-xs text-gray-500 mt-0.5" x-text="'Periode: ' + periods.find(p => p.key === activePeriod)?.label"></p>
                </div>
                <div class="p-5 space-y-4">
                    <div class="flex items-center justify-between p-3 bg-sky-50 rounded-lg">
                        <div>
                            <p class="text-xs text-sky-600 font-semibold">Total Masuk</p>
                            <p class="text-2xl font-bold text-sky-700" x-text="currentStats.totalMasuk"></p>
                        </div>
                        <svg class="w-8 h-8 text-sky-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 8.25H7.5a2.25 2.25 0 00-2.25 2.25v9a2.25 2.25 0 002.25 2.25h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25H15M9 12l3 3m0 0l3-3m-3 3V2.25"/>
                        </svg>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-emerald-50 rounded-lg">
                        <div>
                            <p class="text-xs text-emerald-600 font-semibold">Total Keluar</p>
                            <p class="text-2xl font-bold text-emerald-700" x-text="currentStats.totalKeluar"></p>
                        </div>
                        <svg class="w-8 h-8 text-emerald-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 8.25H7.5a2.25 2.25 0 00-2.25 2.25v9a2.25 2.25 0 002.25 2.25h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25H15m0-3l-3-3m0 0l-3 3m3-3V15"/>
                        </svg>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-amber-50 rounded-lg">
                        <div>
                            <p class="text-xs text-amber-600 font-semibold">Pending</p>
                            <p class="text-2xl font-bold text-amber-700" x-text="currentStats.pending"></p>
                        </div>
                        <svg class="w-8 h-8 text-amber-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- bagian tabel yang digunakan untuk menampilkan daftar pengajuan surat yang sedang diproses --}}
        {{--
            integrasi backend:
            $pengajuanproses = pengajuan::with('anggota')
                ->wherein('status', ['posted', 'approved'])
                ->latest()
                ->get();
        --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-6">
            <div class="px-5 pt-5 pb-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-base font-bold text-gray-900">Pengajuan Sedang Diproses</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Surat yang sedang menunggu verifikasi atau publikasi</p>
                </div>
                <div class="flex flex-col sm:flex-row items-center gap-3">
                    {{-- Input Pencarian Nama --}}
                    <div class="relative w-full sm:w-48">
                        <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                        </svg>
                        <input x-model="searchPengajuan" type="text" placeholder="Cari dokumen..." class="w-full pl-8 pr-3 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 outline-none">
                    </div>
                    
                    {{-- Filter Jenis --}}
                    <select x-model="filterJenis" class="w-full sm:w-auto px-3 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 outline-none bg-white">
                        <option value="">Semua Kategori</option>
                        <option value="Naskah Dinas Arahan">Naskah Dinas Arahan</option>
                        <option value="Naskah Dinas Korespondensi">Naskah Dinas Korespondensi</option>
                        <option value="Naskah Dinas Khusus">Naskah Dinas Khusus</option>
                    </select>

                    {{-- Filter Tanggal --}}
                    <input x-model="filterTanggal" type="date" class="w-full sm:w-auto px-3 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 outline-none text-gray-600">

                    <a href="/pengajuan-surat" class="hidden sm:flex text-xs font-medium text-sky-600 hover:text-sky-700 items-center gap-1 ml-2 shrink-0">
                        Lihat semua
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                        </svg>
                    </a>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="text-left px-5 py-3 font-semibold text-gray-600">JUDUL PENGAJUAN</th>
                            <th class="text-left px-5 py-3 font-semibold text-gray-600 w-24">JENIS</th>
                            <th class="text-left px-5 py-3 font-semibold text-gray-600 w-36">PENGAJU</th>
                            <th class="text-left px-5 py-3 font-semibold text-gray-600 w-32">TGL MASUK</th>
                            <th class="text-center px-5 py-3 font-semibold text-gray-600 w-32">STATUS</th>
                            <th class="text-center px-5 py-3 font-semibold text-gray-600 w-24">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{--
                            integrasi backend: ganti @foreach di bawah dengan data dari controller
                            @foreach($pengajuanproses as $item)
                            <tr>
                                <td>{{ $item->urutan_antrian }}</td>
                                ...dst
                            </tr>
                            @endforeach
                        --}}
                        <template x-for="item in filteredPengajuanProses" :key="item.id">
                            <tr @click="window.location.href = '/pengajuan/' + item.id + '/edit'" class="border-b border-gray-100 hover:bg-gray-50 transition-colors cursor-pointer">
                                <td class="px-5 py-3.5">
                                    <p class="font-medium text-gray-800" x-text="item.judul"></p>
                                    <p class="text-xs text-gray-400 mt-0.5" x-show="item.grup_verifikasi && item.grup_verifikasi !== '-'" x-text="item.grup_verifikasi"></p>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span :class="item.jenis === 'Naskah Dinas Arahan' ? 'bg-sky-100 text-sky-700' : (item.jenis === 'Naskah Dinas Korespondensi' ? 'bg-emerald-100 text-emerald-700' : 'bg-violet-100 text-violet-700')"
                                          class="px-2 py-0.5 rounded text-xs font-bold" x-text="item.jenis"></span>
                                </td>
                                <td class="px-5 py-3.5 text-gray-600 text-xs" x-text="item.pengaju"></td>
                                <td class="px-5 py-3.5 text-gray-500 text-xs" x-text="item.tgl_masuk"></td>
                                <td class="px-5 py-3.5 text-center">
                                    <span :class="{
                                        'bg-amber-100 text-amber-700': item.status === 'Diproses Admin',
                                        'bg-indigo-100 text-indigo-700': item.status === 'Menunggu Verifikasi',
                                        'bg-sky-100 text-sky-700': item.status === 'Diterbitkan',
                                    }" class="px-2.5 py-1 rounded-full text-xs font-semibold whitespace-nowrap inline-block" x-text="item.status"></span>
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
                                        
                                        <template x-if="item.can_delete">
                                            <form :action="'/pengajuan/' + item.id + '/hapus'" method="POST" @submit.prevent="appConfirm('Apakah Anda yakin ingin menghapus pengajuan ini? Ini tidak bisa dikembalikan.', true).then(res => { if(res.isConfirmed) $el.submit() })">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-red-600 border border-red-200 rounded-lg hover:bg-red-50 transition-colors">
                                                    Hapus
                                                </button>
                                            </form>
                                        </template>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
                <div x-show="filteredPengajuanProses.length === 0" class="py-12 text-center text-gray-400">
                    <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                    </svg>
                    <p class="text-sm">Tidak ada pengajuan yang sedang diproses</p>
                </div>
            </div>
        </div>



    </div>

    {{-- bagian skrip yang digunakan untuk mengelola state alpine.js dan merender chart.js --}}
    <script>
    function dashboard(initialPengajuan, initialDokumen, backendChart, backendStats) {
        return {
            activePeriod: 'minggu',

            periods: [
                { key: 'minggu', label: '7 Hari Terakhir' },
            ],
            
            chartData: {
                minggu: backendChart,
            },
            
            summaryStats: {
                minggu: { 
                    totalMasuk: backendChart.masuk.reduce((a, b) => a + b, 0),  
                    totalKeluar: backendChart.keluar.reduce((a, b) => a + b, 0),   
                    pending: backendStats.pengajuan_proses 
                },
            },

            get currentStats() {
                return this.summaryStats[this.activePeriod];
            },
            
            pengajuanProses: initialPengajuan || [],
            searchPengajuan: '',
            filterJenis: '',
            filterTanggal: '',

            get filteredPengajuanProses() {
                return this.pengajuanProses.filter(item => {
                    const matchSearch = !this.searchPengajuan || 
                        item.judul.toLowerCase().includes(this.searchPengajuan.toLowerCase()) || 
                        item.pengaju.toLowerCase().includes(this.searchPengajuan.toLowerCase());
                    const matchJenis = !this.filterJenis || (item.jenis && item.jenis.includes(this.filterJenis));
                    const matchTanggal = !this.filterTanggal || item.raw_tgl_masuk === this.filterTanggal;
                    return matchSearch && matchJenis && matchTanggal;
                });
            },

            dokumenTerbaru: initialDokumen || [],

            // Chart instance
            chartInstance: null,

            setPeriod(key) {
                this.activePeriod = key;
                this.updateChart();
            },

            updateChart() {
                if (!this.chartInstance) return;
                const data = this.chartData[this.activePeriod];
                this.chartInstance.data.labels = data.labels;
                this.chartInstance.data.datasets[0].data = data.masuk;
                this.chartInstance.data.datasets[1].data = data.keluar;
                this.chartInstance.update('active');
            },

            initChart() {
                const ctx = document.getElementById('suratChart').getContext('2d');
                const data = this.chartData[this.activePeriod];

                this.chartInstance = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [
                            {
                                label: 'Surat Masuk',
                                data: data.masuk,
                                borderColor: '#0ea5e9',
                                backgroundColor: 'rgba(14, 165, 233, 0.08)',
                                borderWidth: 2.5,
                                pointBackgroundColor: '#0ea5e9',
                                pointRadius: 4,
                                pointHoverRadius: 6,
                                tension: 0.4,
                                fill: true,
                            },
                            {
                                label: 'Surat Keluar',
                                data: data.keluar,
                                borderColor: '#10b981',
                                backgroundColor: 'rgba(16, 185, 129, 0.08)',
                                borderWidth: 2.5,
                                pointBackgroundColor: '#10b981',
                                pointRadius: 4,
                                pointHoverRadius: 6,
                                tension: 0.4,
                                fill: true,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#1e293b',
                                titleColor: '#94a3b8',
                                bodyColor: '#f1f5f9',
                                padding: 10,
                                cornerRadius: 8,
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: { color: '#94a3b8', font: { size: 11 } }
                            },
                            y: {
                                beginAtZero: true,
                                grid: { color: '#f1f5f9' },
                                ticks: { color: '#94a3b8', font: { size: 11 }, stepSize: 1 }
                            }
                        }
                    }
                });
            },

            init() {
                this.$nextTick(() => this.initChart());
            }
        };
    }
    </script>

        </div>
    </div>
@endsection