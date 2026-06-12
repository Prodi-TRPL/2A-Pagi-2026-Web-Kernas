@extends('layouts.app')

@section('title', 'Manajemen Peraturan — KERNAS')

@section('content')
<div class="bg-gray-50 min-h-screen font-sans" x-data="manajemenPeraturan()">
    <div class="max-w-screen-xl mx-auto px-4 py-8">

        {{-- bagian yang digunakan untuk menampilkan judul dan header halaman --}}
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Manajemen Peraturan</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola daftar peraturan yang tersedia sebagai referensi saat pengajuan surat</p>
        </div>

        {{-- bagian yang digunakan untuk menampilkan toolbar aksi --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-4 p-4">
            <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center justify-between">

                {{-- bagian yang digunakan untuk menampilkan input pencarian dan filter --}}
                <div class="flex flex-col sm:flex-row gap-3 flex-1">
                    <div class="relative flex-1 max-w-sm">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                        </svg>
                        <input
                            x-model="search"
                            @input.debounce.300ms="currentPage = 1"
                            type="text"
                            placeholder="Cari kode atau judul peraturan..."
                            class="w-full pl-9 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                        >
                    </div>
                    <select x-model="filterJenis" @change="currentPage = 1"
                            class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 bg-white">
                        <option value="">Semua Jenis</option>
                        <option value="UU">Undang-Undang (UU)</option>
                        <option value="PP">Peraturan Pemerintah (PP)</option>
                        <option value="Perpres">Peraturan Presiden (Perpres)</option>
                        <option value="Permendikbud">Permendikbud</option>
                        <option value="Permenristek">Permenristek</option>
                        <option value="SK Direktur">SK Direktur</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                {{-- bagian tombol yang digunakan untuk menambah data baru --}}
                <button @click="openModal('tambah')"
                        class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-sky-600 rounded-lg hover:bg-sky-700 transition-colors shadow-sm flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                    Tambah Peraturan
                </button>
            </div>
        </div>

        {{-- bagian tabel yang digunakan untuk menampilkan daftar karyawan --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left px-5 py-3 font-semibold text-gray-600 w-10">#</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600 w-36">KODE / NOMOR</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600">JUDUL PERATURAN</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600 w-32">JENIS</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600 w-24">TAHUN</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600 w-28">DITAMBAH OLEH</th>
                        <th class="text-center px-5 py-3 font-semibold text-gray-600 w-28">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(item, index) in paginatedData" :key="item.id">
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-3.5 text-gray-400 text-xs" x-text="(currentPage - 1) * perPage + index + 1"></td>
                            <td class="px-5 py-3.5">
                                <span class="font-mono text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded" x-text="item.kode"></span>
                            </td>
                            <td class="px-5 py-3.5">
                                <p class="font-medium text-gray-800 leading-snug" x-text="item.judul"></p>
                                <p class="text-xs text-gray-400 mt-0.5" x-text="item.keterangan" x-show="item.keterangan"></p>
                            </td>
                            <td class="px-5 py-3.5">
                                <span :class="{
                                    'bg-blue-100 text-blue-700':   item.jenis === 'UU',
                                    'bg-purple-100 text-purple-700': item.jenis === 'PP',
                                    'bg-indigo-100 text-indigo-700': item.jenis === 'Perpres',
                                    'bg-sky-100 text-sky-700':     item.jenis === 'Permendikbud',
                                    'bg-teal-100 text-teal-700':   item.jenis === 'Permenristek',
                                    'bg-orange-100 text-orange-700': item.jenis === 'SK Direktur',
                                    'bg-gray-100 text-gray-600':   item.jenis === 'Lainnya',
                                }" class="px-2.5 py-1 rounded-full text-xs font-semibold" x-text="item.jenis"></span>
                            </td>
                            <td class="px-5 py-3.5 text-gray-600 text-sm" x-text="item.tahun"></td>
                            <td class="px-5 py-3.5 text-gray-500 text-xs" x-text="item.ditambah_oleh"></td>
                            <td class="px-5 py-3.5 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button @click="openModal('edit', item)"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg border border-amber-300 text-amber-700 bg-amber-50 hover:bg-amber-100 hover:border-amber-400 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                                        </svg>
                                        Edit
                                    </button>
                                    <button @click="hapusPeraturan(item.id)"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg border border-red-200 text-red-600 bg-red-50 hover:bg-red-100 hover:border-red-300 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                        </svg>
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>

                    {{-- bagian yang ditampilkan ketika data tabel kosong atau tidak ditemukan --}}
                    <tr x-show="paginatedData.length === 0" x-cloak>
                        <td colspan="7" class="px-5 py-14 text-center text-gray-400">
                            <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                            </svg>
                            <p class="text-sm">Tidak ada peraturan ditemukan</p>
                        </td>
                    </tr>
                </tbody>
            </table>

            {{-- bagian yang digunakan untuk menampilkan navigasi halaman (pagination) --}}
            <div class="px-5 py-3 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    <span>Tampilkan</span>
                    <select x-model="perPage" @change="currentPage = 1" class="px-2 py-1 border border-gray-300 rounded text-sm">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="25">25</option>
                    </select>
                    <span>data per halaman</span>
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    <span x-text="`${filteredData.length === 0 ? 0 : (currentPage - 1) * perPage + 1}–${Math.min(currentPage * perPage, filteredData.length)} dari ${filteredData.length}`"></span>
                    <div class="flex items-center gap-1">
                        <button @click="prevPage" :disabled="currentPage === 1" class="p-1.5 rounded border border-gray-300 hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
                            </svg>
                        </button>
                        <button @click="nextPage" :disabled="currentPage >= totalPages" class="p-1.5 rounded border border-gray-300 hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- bagian utama yang digunakan untuk menampilkan modal form tambah atau edit --}}
    <div
        x-show="modalOpen"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        @click.self="closeModal"
    >
        <div class="absolute inset-0 bg-black/50"></div>

        <div
            x-show="modalOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative bg-white rounded-2xl shadow-xl w-full max-w-lg z-10"
        >
            {{-- bagian atas (header) dari modal yang berisi judul dan tombol tutup --}}
            <div class="px-6 pt-6 pb-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-gray-900" x-text="modalMode === 'tambah' ? 'Tambah Peraturan Baru' : 'Edit Peraturan'"></h2>
                    <p class="text-xs text-gray-500 mt-0.5">Peraturan yang ditambahkan akan tersedia sebagai pilihan di form pengajuan</p>
                </div>
                <button @click="closeModal" class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- bagian tengah (body) dari modal yang berisi form isian --}}
            <div class="px-6 py-5 space-y-4">

                {{-- bagian input form yang digunakan untuk mengisi kode atau nomor dokumen --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Kode / Nomor Peraturan <span class="text-red-500">*</span>
                    </label>
                    <input
                        x-model="form.kode"
                        type="text"
                        placeholder="Contoh: UU No. 12 Tahun 2012"
                        class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500"
                        :class="errors.kode ? 'border-red-400 bg-red-50' : 'border-gray-300'"
                    >
                    <p class="text-xs text-red-500 mt-1" x-show="errors.kode" x-text="errors.kode"></p>
                </div>

                {{-- bagian input form yang digunakan untuk mengisi judul --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Judul Peraturan <span class="text-red-500">*</span>
                    </label>
                    <input
                        x-model="form.judul"
                        type="text"
                        placeholder="Contoh: Pendidikan Tinggi"
                        class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500"
                        :class="errors.judul ? 'border-red-400 bg-red-50' : 'border-gray-300'"
                    >
                    <p class="text-xs text-red-500 mt-1" x-show="errors.judul" x-text="errors.judul"></p>
                </div>

                {{-- bagian input form yang digunakan untuk memilih jenis dan tahun (ditampilkan sejajar) --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                            Jenis <span class="text-red-500">*</span>
                        </label>
                        <select
                            x-model="form.jenis"
                            class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 bg-white"
                            :class="errors.jenis ? 'border-red-400 bg-red-50' : 'border-gray-300'"
                        >
                            <option value="">-- Pilih jenis --</option>
                            <option value="UU">Undang-Undang (UU)</option>
                            <option value="PP">Peraturan Pemerintah (PP)</option>
                            <option value="Perpres">Peraturan Presiden (Perpres)</option>
                            <option value="Permendikbud">Permendikbud</option>
                            <option value="Permenristek">Permenristek</option>
                            <option value="SK Direktur">SK Direktur</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                        <p class="text-xs text-red-500 mt-1" x-show="errors.jenis" x-text="errors.jenis"></p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                            Tahun <span class="text-red-500">*</span>
                        </label>
                        <input
                            x-model="form.tahun"
                            type="number"
                            min="1945"
                            max="2099"
                            placeholder="2024"
                            class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500"
                            :class="errors.tahun ? 'border-red-400 bg-red-50' : 'border-gray-300'"
                        >
                        <p class="text-xs text-red-500 mt-1" x-show="errors.tahun" x-text="errors.tahun"></p>
                    </div>
                </div>

                {{-- bagian input form tambahan yang digunakan untuk mengisi keterangan opsional --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Keterangan <span class="text-gray-400 font-normal">(opsional)</span>
                    </label>
                    <textarea
                        x-model="form.keterangan"
                        rows="2"
                        placeholder="Deskripsi singkat tentang isi peraturan ini..."
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 resize-none"
                    ></textarea>
                </div>

                {{-- bagian yang digunakan untuk melihat pratinjau (preview) hasil input --}}
                <div x-show="form.kode || form.judul" class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                    <p class="text-xs text-gray-500 mb-1 font-semibold">PREVIEW TEKS MENIMBANG</p>
                    <p class="text-xs text-gray-700 leading-relaxed">
                        Bahwa sesuai dengan <span class="font-semibold" x-text="form.kode || '...'"></span>
                        tentang <span class="font-semibold" x-text="form.judul || '...'"></span>, perlu ditetapkan...
                    </p>
                </div>

            </div>

            {{-- bagian bawah (footer) dari modal --}}
            <div class="px-6 pb-6 flex items-center justify-end">

                <div class="flex items-center gap-3">
                    <button @click="closeModal" class="px-4 py-2 text-sm font-medium text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button @click="simpanPeraturan" class="px-4 py-2 text-sm font-medium text-white bg-sky-600 rounded-lg hover:bg-sky-700 transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                        </svg>
                        <span x-text="modalMode === 'tambah' ? 'Simpan Peraturan' : 'Simpan Perubahan'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- bagian yang digunakan untuk menampilkan popup notifikasi (toast) --}}
    <div
        x-show="toast.show"
        x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed bottom-6 right-6 z-50 flex items-center gap-3 px-4 py-3 rounded-xl shadow-xl text-sm font-medium"
        :class="toast.type === 'success' ? 'bg-gray-900 text-white' : 'bg-red-600 text-white'"
    >
        <svg x-show="toast.type === 'success'" class="w-5 h-5 text-green-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <svg x-show="toast.type === 'error'" class="w-5 h-5 text-white flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
        </svg>
        <span x-text="toast.message"></span>
    </div>

    {{-- bagian skrip yang digunakan untuk menyimpan data dan fungsi (state) alpine.js --}}
    <script>
    function manajemenPeraturan() {
        return {
            search: '',
            filterJenis: '',
            perPage: 10,
            currentPage: 1,

            modalOpen: false,
            modalMode: 'tambah', // 'tambah' | 'edit'
            editId: null,

            form: { kode: '', judul: '', jenis: '', tahun: '', keterangan: '' },
            errors: {},

            toast: { show: false, message: '', type: 'success' },

            peraturan: [
                { id: 1,  kode: 'UU No. 12 Tahun 2012',           judul: 'Pendidikan Tinggi',                                              jenis: 'UU',           tahun: 2012, keterangan: 'Dasar hukum penyelenggaraan pendidikan tinggi di Indonesia', ditambah_oleh: 'Admin' },
                { id: 2,  kode: 'UU No. 20 Tahun 2003',           judul: 'Sistem Pendidikan Nasional',                                     jenis: 'UU',           tahun: 2003, keterangan: 'Landasan sistem pendidikan nasional',                          ditambah_oleh: 'Admin' },
                { id: 3,  kode: 'PP No. 4 Tahun 2014',            judul: 'Penyelenggaraan Pendidikan Tinggi dan Pengelolaan Perguruan Tinggi', jenis: 'PP',        tahun: 2014, keterangan: '',                                                           ditambah_oleh: 'Admin' },
                { id: 4,  kode: 'PP No. 37 Tahun 2009',           judul: 'Dosen',                                                          jenis: 'PP',           tahun: 2009, keterangan: 'Peraturan mengenai dosen sebagai tenaga pendidik',            ditambah_oleh: 'Admin' },
                { id: 5,  kode: 'Perpres No. 8 Tahun 2012',       judul: 'Kerangka Kualifikasi Nasional Indonesia (KKNI)',                  jenis: 'Perpres',      tahun: 2012, keterangan: 'Standar kompetensi lulusan perguruan tinggi',                ditambah_oleh: 'Admin' },
                { id: 6,  kode: 'Permendikbud No. 3 Tahun 2020',  judul: 'Standar Nasional Pendidikan Tinggi',                             jenis: 'Permendikbud', tahun: 2020, keterangan: 'SN-Dikti yang mengatur standar mutu pendidikan tinggi',      ditambah_oleh: 'Admin' },
                { id: 7,  kode: 'Permendikbud No. 7 Tahun 2020',  judul: 'Pendirian, Perubahan, Pembubaran PTN',                           jenis: 'Permendikbud', tahun: 2020, keterangan: '',                                                           ditambah_oleh: 'Admin' },
                { id: 8,  kode: 'Permendikbud No. 53 Tahun 2023', judul: 'Penjaminan Mutu Pendidikan Tinggi',                              jenis: 'Permendikbud', tahun: 2023, keterangan: 'Ketentuan penjaminan mutu internal dan eksternal',            ditambah_oleh: 'Budi Santoso' },
                { id: 9,  kode: 'Permenristek No. 44 Tahun 2015', judul: 'Standar Nasional Pendidikan Tinggi',                             jenis: 'Permenristek',tahun: 2015, keterangan: '',                                                            ditambah_oleh: 'Admin' },
                { id: 10, kode: 'SK Dir No. 001/SK/2024',         judul: 'Pedoman Akademik Politeknik Negeri Batam 2024',                  jenis: 'SK Direktur',  tahun: 2024, keterangan: 'Pedoman akademik internal Polibatam',                        ditambah_oleh: 'Admin' },
                { id: 11, kode: 'SK Dir No. 012/SK/2025',         judul: 'Struktur Organisasi dan Tata Kelola Polibatam',                  jenis: 'SK Direktur',  tahun: 2025, keterangan: '',                                                           ditambah_oleh: 'Admin' },
                { id: 12, kode: 'SK Dir No. 025/SK/2025',         judul: 'Pedoman Pengajuan dan Distribusi Surat Keputusan Internal',      jenis: 'SK Direktur',  tahun: 2025, keterangan: 'Mengatur alur pengajuan dan distribusi SK di lingkungan Polibatam', ditambah_oleh: 'Dodi Prasojo' },
            ],

            get filteredData() {
                return this.peraturan.filter(p => {
                    const matchSearch = !this.search ||
                        p.kode.toLowerCase().includes(this.search.toLowerCase()) ||
                        p.judul.toLowerCase().includes(this.search.toLowerCase());
                    const matchJenis = !this.filterJenis || p.jenis === this.filterJenis;
                    return matchSearch && matchJenis;
                });
            },

            get totalPages() {
                return Math.max(1, Math.ceil(this.filteredData.length / this.perPage));
            },

            get paginatedData() {
                const start = (this.currentPage - 1) * this.perPage;
                return this.filteredData.slice(start, start + parseInt(this.perPage));
            },

            prevPage() { if (this.currentPage > 1) this.currentPage--; },
            nextPage()  { if (this.currentPage < this.totalPages) this.currentPage++; },

            openModal(mode, item = null) {
                this.modalMode = mode;
                this.errors = {};
                if (mode === 'edit' && item) {
                    this.editId = item.id;
                    this.form = { kode: item.kode, judul: item.judul, jenis: item.jenis, tahun: item.tahun, keterangan: item.keterangan };
                } else {
                    this.editId = null;
                    this.form = { kode: '', judul: '', jenis: '', tahun: new Date().getFullYear(), keterangan: '' };
                }
                this.modalOpen = true;
            },

            closeModal() {
                this.modalOpen = false;
                this.errors = {};
            },

            validate() {
                this.errors = {};
                if (!this.form.kode.trim())  this.errors.kode  = 'Kode / nomor peraturan wajib diisi';
                if (!this.form.judul.trim()) this.errors.judul = 'Judul peraturan wajib diisi';
                if (!this.form.jenis)        this.errors.jenis = 'Jenis peraturan wajib dipilih';
                if (!this.form.tahun)        this.errors.tahun = 'Tahun wajib diisi';
                return Object.keys(this.errors).length === 0;
            },

            simpanPeraturan() {
                if (!this.validate()) return;

                if (this.modalMode === 'tambah') {
                    // INTEGRASI BACKEND: fetch POST /setup/peraturan
                    const newId = Math.max(...this.peraturan.map(p => p.id)) + 1;
                    this.peraturan.unshift({
                        id: newId,
                        kode: this.form.kode,
                        judul: this.form.judul,
                        jenis: this.form.jenis,
                        tahun: parseInt(this.form.tahun),
                        keterangan: this.form.keterangan,
                        ditambah_oleh: 'Admin',
                    });
                    this.showToast('Peraturan berhasil ditambahkan', 'success');
                } else {
                    // INTEGRASI BACKEND: fetch PATCH /setup/peraturan/{id}
                    const idx = this.peraturan.findIndex(p => p.id === this.editId);
                    if (idx !== -1) {
                        this.peraturan[idx] = {
                            ...this.peraturan[idx],
                            kode: this.form.kode,
                            judul: this.form.judul,
                            jenis: this.form.jenis,
                            tahun: parseInt(this.form.tahun),
                            keterangan: this.form.keterangan,
                        };
                    }
                    this.showToast('Peraturan berhasil diperbarui', 'success');
                }
                this.closeModal();
            },

            hapusPeraturan(id) {
                if (!confirm('Yakin ingin menghapus peraturan ini?')) return;
                // INTEGRASI BACKEND: fetch DELETE /setup/peraturan/{id}
                this.peraturan = this.peraturan.filter(p => p.id !== id);
                this.showToast('Peraturan berhasil dihapus', 'success');
            },

            showToast(message, type = 'success') {
                this.toast = { show: true, message, type };
                setTimeout(() => { this.toast.show = false; }, 3000);
            },
        };
    }
    </script>

</div>
</div>
@endsection
