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
                        } elseif (stripos($tmpl->filepath, 'dinas_instruksi') !== false || stripos($tmpl->nama_template, 'Instruksi') !== false) {
                            $formUrl = '/pengajuan/form/dinas-instruksi';
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
                        } elseif (stripos($tmpl->filepath, 'dinas_instruksi') !== false || stripos($tmpl->nama_template, 'Instruksi') !== false) {
                            $formUrl = '/pengajuan/form/dinas-instruksi';
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
                    <option value="{{ $formUrl }}">{{ $tmpl->nama_template }}</option>
                @endforeach
            </select>
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
                                <a :href="'/pengajuan/' + item.id + '/edit'" class="text-sky-600 hover:text-sky-800 text-xs font-medium hover:underline">
                                    Buka Draf
                                </a>
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
