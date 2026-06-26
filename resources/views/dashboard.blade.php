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
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">

            {{-- Dropdown Naskah Dinas Arahan --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Naskah Dinas Arahan</label>
                <select onchange="if(this.value && this.value !== '#') window.location.href=this.value;" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent outline-none text-sm text-gray-700 cursor-pointer">
                    <option value="">-- Pilih Surat --</option>
                    @foreach($templatesArahan ?? [] as $tmpl)
                        @php
                            $formUrl = '#';
                            if (stripos($tmpl->filepath, 'contoh_surat_satu') !== false || stripos($tmpl->nama_template, 'SK') !== false) {
                                $formUrl = '/pengajuan/form/contoh-surat-satu';
                            }
                        @endphp
                        <option value="{{ $formUrl }}">{{ $tmpl->nama_template }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Dropdown Naskah Dinas Korespodensi --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Naskah Dinas Korespodensi</label>
                <select onchange="if(this.value && this.value !== '#') window.location.href=this.value;" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent outline-none text-sm text-gray-700 cursor-pointer">
                    <option value="">-- Pilih Surat --</option>
                    @foreach($templatesKorespondensi ?? [] as $tmpl)
                        @php
                            $formUrl = '#';
                            if (stripos($tmpl->filepath, 'contoh_surat_satu') !== false || stripos($tmpl->nama_template, 'SK') !== false) {
                                $formUrl = '/pengajuan/form/contoh-surat-satu';
                            }
                        @endphp
                        <option value="{{ $formUrl }}">{{ $tmpl->nama_template }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Dropdown Naskah Dinas Khusus --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Naskah Dinas Khusus</label>
                <select onchange="if(this.value && this.value !== '#') window.location.href=this.value;" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent outline-none text-sm text-gray-700 cursor-pointer">
                    <option value="">-- Pilih Surat --</option>
                    @foreach($templatesKhusus ?? [] as $tmpl)
                        @php
                            $formUrl = '#';
                            if (stripos($tmpl->filepath, 'contoh_surat_satu') !== false || stripos($tmpl->nama_template, 'SK') !== false) {
                                $formUrl = '/pengajuan/form/contoh-surat-satu';
                            }
                        @endphp
                        <option value="{{ $formUrl }}">{{ $tmpl->nama_template }}</option>
                    @endforeach
                </select>
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
                    <div class="pt-1 border-t border-gray-100">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Tingkat Penyelesaian</span>
                            <span class="font-bold text-gray-800" x-text="currentStats.totalMasuk > 0 ? Math.round((currentStats.totalKeluar / currentStats.totalMasuk) * 100) + '%' : '0%'"></span>
                        </div>
                        <div class="mt-2 h-2 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-emerald-500 rounded-full transition-all duration-500"
                                 :style="'width: ' + (currentStats.totalMasuk > 0 ? Math.round((currentStats.totalKeluar / currentStats.totalMasuk) * 100) : 0) + '%'">
                            </div>
                        </div>
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
                        <option value="">Semua Jenis</option>
                        <option value="SK">Surat Keputusan (SK)</option>
                        <option value="ST">Surat Tugas (ST)</option>
                        <option value="SE">Surat Edaran (SE)</option>
                        <option value="ND">Nota Dinas (ND)</option>
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
                            <th class="text-left px-5 py-3 font-semibold text-gray-600 w-36">NO. ANTRIAN</th>
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
                                    <span class="font-mono text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded" x-text="'#' + String(item.urutan_antrian).padStart(3, '0')"></span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <p class="font-medium text-gray-800" x-text="item.judul"></p>
                                    <p class="text-xs text-gray-400 mt-0.5" x-text="item.grup_verifikasi"></p>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span :class="item.jenis === 'SK' ? 'bg-sky-100 text-sky-700' : 'bg-violet-100 text-violet-700'"
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
                                            <form :action="'/pengajuan/' + item.id + '/hapus'" method="POST" @submit="return confirm('Apakah Anda yakin ingin menghapus pengajuan ini? Ini tidak bisa dikembalikan.')">
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

        {{-- bagian daftar yang digunakan untuk menampilkan 5 dokumen terbaru yang telah diterbitkan --}}
        {{--
            integrasi backend:
            $dokumenterbaru = dokumen::latest('created_at')->take(5)->get();
        --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-5 pt-5 pb-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-gray-900">Dokumen Terbaru Diterbitkan</h2>
                    <p class="text-xs text-gray-500 mt-0.5">5 dokumen yang paling baru dipublikasikan</p>
                </div>
                <a href="/surat-keputusan" class="text-xs font-medium text-sky-600 hover:text-sky-700 flex items-center gap-1">
                    Lihat semua
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                    </svg>
                </a>
            </div>
            <div class="divide-y divide-gray-100">
                <template x-for="dok in dokumenTerbaru" :key="dok.id">
                    <div @click="window.location.href = '/dokumen/' + dok.id" class="px-5 py-3.5 flex items-center gap-4 hover:bg-gray-50 transition-colors cursor-pointer">
                        <div :class="dok.jenis === 'SK' ? 'bg-sky-100 text-sky-700' : 'bg-violet-100 text-violet-700'"
                             class="w-10 h-10 rounded-lg flex items-center justify-center text-xs font-bold flex-shrink-0"
                             x-text="dok.jenis"></div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate" x-text="dok.judul"></p>
                            <p class="text-xs text-gray-400 mt-0.5" x-text="dok.nomor + ' · ' + dok.tgl_terbit"></p>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0" @click.stop>
                            <span class="text-xs text-gray-500" x-text="dok.dibuat_oleh"></span>
                            <a :href="'/dokumen/' + dok.id" class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 hover:text-sky-600 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </template>
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
                    const matchJenis = !this.filterJenis || item.jenis === this.filterJenis;
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