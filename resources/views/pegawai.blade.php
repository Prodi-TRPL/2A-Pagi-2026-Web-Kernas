@extends('layouts.app')

@section('title', 'Manajemen Karyawan — KERNAS')

@section('content')
    <div x-data="manajemenKaryawan(@js($karyawan), @js($grupVerifikasi))">
        <div class="max-w-screen-xl mx-auto px-4 py-8">

        {{-- bagian yang digunakan untuk menampilkan header halaman --}}
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Manajemen Karyawan</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola hak akses dan peran karyawan Polibatam</p>
        </div>

        {{-- bagian yang digunakan untuk menampilkan kotak pencarian dan filter --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-4 p-4 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex flex-col sm:flex-row gap-3 flex-1 w-full md:w-auto">
                {{-- bagian input yang digunakan untuk mengetik pencarian --}}
                <div class="relative flex-1">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                    </svg>
                    <input
                        x-model="search"
                        @input.debounce.300ms="currentPage = 1"
                        type="text"
                        placeholder="Cari nama atau NIP..."
                        class="w-full pl-9 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                    >
                </div>

                {{-- bagian dropdown yang digunakan untuk menyaring data berdasarkan unit --}}
                <select
                    x-model="filterUnit"
                    @change="currentPage = 1"
                    class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 bg-white"
                >
                    <option value="">Semua Unit</option>
                    <option value="Jurusan Teknik Elektro">Jurusan Teknik Elektro</option>
                    <option value="Jurusan Teknik Mesin">Jurusan Teknik Mesin</option>
                    <option value="Jurusan Manajemen dan Bisnis">Jurusan Manajemen dan Bisnis</option>
                    <option value="Jurusan Teknik Informatika">Jurusan Teknik Informatika</option>
                </select>

                {{-- bagian dropdown yang digunakan untuk menyaring data berdasarkan peran (role) --}}
                <select
                    x-model="filterRole"
                    @change="currentPage = 1"
                    class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 bg-white"
                >
                    <option value="">Semua Peran</option>
                    <option value="admin">Admin</option>
                    <option value="verifikator">Verifikator</option>
                    <option value="staff">Pengguna Biasa</option>
                </select>
            </div>
            
            {{-- Tombol Tambah Pengguna --}}
            <button @click="openTambahModal()" class="flex-shrink-0 w-full md:w-auto px-4 py-2 text-sm font-medium text-white bg-sky-600 rounded-lg hover:bg-sky-700 transition-colors flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Tambah Pengguna
            </button>
        </div>

        {{-- bagian tabel yang digunakan untuk menampilkan daftar karyawan --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 w-48">NIP</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600">NAMA</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 w-36">JABATAN</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600">UNIT</th>
                        <th class="text-center px-4 py-3 font-semibold text-gray-600 w-32">PERAN</th>
                        <th class="text-center px-4 py-3 font-semibold text-gray-600 w-36">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(karyawan, index) in paginatedData" :key="karyawan.nip">
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 text-gray-600 font-mono text-xs" x-text="karyawan.nip"></td>
                            <td class="px-4 py-3 font-medium text-gray-800" x-text="karyawan.nama"></td>
                            <td class="px-4 py-3 text-gray-600" x-text="karyawan.jabatan"></td>
                            <td class="px-4 py-3 text-gray-600" x-text="karyawan.unit"></td>

                            {{-- bagian label (badge) yang digunakan untuk menunjukkan peran karyawan saat ini --}}
                            <td class="px-4 py-3 text-center">
                                <span
                                    :class="{
                                        'bg-red-100 text-red-700': karyawan.role === 'admin',
                                        'bg-amber-100 text-amber-700': karyawan.role === 'verifikator',
                                        'bg-gray-100 text-gray-600': karyawan.role === 'staff'
                                    }"
                                    class="px-2.5 py-1 rounded-full text-xs font-semibold"
                                    x-text="karyawan.role === 'admin' ? 'Admin' : karyawan.role === 'verifikator' ? 'Verifikator' : 'Pengguna'"
                                ></span>
                            </td>

                            {{-- bagian tombol yang digunakan untuk membuka modal pengaturan peran --}}
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button
                                        @click="openProfilModal(karyawan)"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg border border-teal-300 text-teal-700 bg-teal-50 hover:bg-teal-100 hover:border-teal-400 transition-colors"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125"/>
                                        </svg>
                                        Edit Profil
                                    </button>
                                    <button
                                        @click="openRoleModal(karyawan)"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg border border-sky-300 text-sky-700 bg-sky-50 hover:bg-sky-100 hover:border-sky-400 transition-colors"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 011.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.559.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.894.149c-.424.07-.764.383-.929.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 01-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.398.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 01-.12-1.45l.527-.737c.25-.35.272-.806.108-1.204-.165-.397-.506-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.108-1.204l-.526-.738a1.125 1.125 0 01.12-1.45l.773-.773a1.125 1.125 0 011.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        Atur Peran
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>

                    {{-- bagian yang ditampilkan ketika data tabel kosong atau tidak ditemukan --}}
                    <tr x-show="paginatedData.length === 0" x-cloak>
                        <td colspan="6" class="px-4 py-12 text-center text-gray-400">
                            <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 16.318A4.486 4.486 0 0012.016 15a4.486 4.486 0 00-3.198 1.318M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75zm-.375 0h.008v.015h-.008V9.75zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75zm-.375 0h.008v.015h-.008V9.75z"/>
                            </svg>
                            <p class="text-sm">Tidak ada karyawan ditemukan</p>
                        </td>
                    </tr>
                </tbody>
            </table>

            {{-- bagian yang digunakan untuk menampilkan navigasi halaman (pagination) --}}
            <div class="px-4 py-3 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-3">
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
                    <span x-text="`${(currentPage - 1) * perPage + 1}–${Math.min(currentPage * perPage, filteredData.length)} dari ${filteredData.length}`"></span>
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

    {{-- bagian yang digunakan untuk menampilkan modal atur peran --}}
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
        {{-- bagian latar belakang gelap (backdrop) saat modal terbuka --}}
        <div class="absolute inset-0 bg-black/50"></div>

        {{-- bagian kotak utama dari modal --}}
        <div
            x-show="modalOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative bg-white rounded-2xl shadow-xl w-full max-w-md z-10"
        >
            {{-- bagian atas (header) dari modal yang berisi judul dan tombol tutup --}}
            <div class="px-6 pt-6 pb-4 border-b border-gray-100">
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Atur Peran Karyawan</h2>
                        <p class="text-sm text-gray-500 mt-0.5" x-text="selectedKaryawan ? selectedKaryawan.nama : ''"></p>
                    </div>
                    <button @click="closeModal" class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- bagian tengah (body) dari modal yang berisi form isian --}}
            <div class="px-6 py-5">

                {{-- bagian yang digunakan untuk menampilkan informasi singkat karyawan di dalam modal --}}
                <div class="bg-gray-50 rounded-lg p-3 mb-5 flex items-center gap-3" x-show="selectedKaryawan">
                    <div class="w-10 h-10 rounded-full bg-sky-100 flex items-center justify-center text-sky-700 font-bold text-sm flex-shrink-0"
                         x-text="selectedKaryawan ? selectedKaryawan.nama.substring(0, 2).toUpperCase() : ''">
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800" x-text="selectedKaryawan ? selectedKaryawan.nama : ''"></p>
                        <p class="text-xs text-gray-500" x-text="selectedKaryawan ? `NIP: ${selectedKaryawan.nip} · ${selectedKaryawan.unit}` : ''"></p>
                    </div>
                </div>

                {{-- bagian pilihan radio button untuk menetapkan peran --}}
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Pilih Peran</p>
                <div class="space-y-2">
                    {{-- pilihan peran sebagai admin --}}
                    <label class="flex items-start gap-3 p-3.5 rounded-xl border-2 cursor-pointer transition-all"
                           :class="selectedRole === 'admin' ? 'border-red-400 bg-red-50' : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50'">
                        <input type="radio" x-model="selectedRole" value="admin" class="mt-0.5 accent-red-500">
                        <div>
                            <p class="text-sm font-semibold text-gray-800">Admin</p>
                            <p class="text-xs text-gray-500 mt-0.5">Akses penuh: buat, edit, publish, dan kelola semua dokumen dan pengguna</p>
                        </div>
                    </label>

                    {{-- pilihan peran sebagai verifikator --}}
                    <div class="rounded-xl border-2 transition-all"
                         :class="selectedRole === 'verifikator' ? 'border-amber-400 bg-amber-50' : 'border-gray-200 hover:border-gray-300'">
                        <label class="flex items-start gap-3 p-3.5 cursor-pointer">
                            <input type="radio" x-model="selectedRole" value="verifikator" class="mt-0.5 accent-amber-500">
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-800">Verifikator</p>
                                <p class="text-xs text-gray-500 mt-0.5">Dapat menyetujui atau menolak pengajuan surat dari staff</p>
                            </div>
                        </label>

                        {{-- bagian dropdown ekstra yang muncul jika peran verifikator dipilih, digunakan untuk memilih grup --}}
                        <div x-show="selectedRole === 'verifikator'"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="px-3.5 pb-3.5">
                            <label class="block text-xs font-semibold text-amber-700 mb-1.5">Pilih Grup Verifikasi</label>
                            <select x-model="selectedGrup"
                                    class="w-full px-3 py-2 text-sm border border-amber-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-amber-400">
                                <option value="">-- Pilih grup --</option>
                                <template x-for="grup in grupVerifikasi" :key="grup.id">
                                    <option :value="grup.id" x-text="grup.nama"></option>
                                </template>
                            </select>
                            <p class="text-xs text-amber-600 mt-1.5" x-show="!selectedGrup">
                                Pilih grup verifikasi terlebih dahulu
                            </p>
                        </div>
                    </div>

                    {{-- pilihan peran sebagai pengguna biasa --}}
                    <label class="flex items-start gap-3 p-3.5 rounded-xl border-2 cursor-pointer transition-all"
                           :class="selectedRole === 'staff' ? 'border-sky-400 bg-sky-50' : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50'">
                        <input type="radio" x-model="selectedRole" value="staff" class="mt-0.5 accent-sky-500">
                        <div>
                            <p class="text-sm font-semibold text-gray-800">Pengguna</p>
                            <p class="text-xs text-gray-500 mt-0.5">Hanya bisa mengajukan surat dan melihat dokumen yang ditugaskan</p>
                        </div>
                    </label>
                </div>
            </div>

            {{-- bagian bawah (footer) dari modal yang berisi tombol batal dan simpan --}}
            <div class="px-6 pb-6 flex items-center gap-3 justify-end">
                <button @click="closeModal" class="px-4 py-2 text-sm font-medium text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button @click="saveRole" class="px-4 py-2 text-sm font-medium text-white bg-sky-600 rounded-lg hover:bg-sky-700 transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                    </svg>
                    Simpan Peran
                </button>
            </div>
        </div>
    </div>

    {{-- bagian yang digunakan untuk menampilkan modal edit profil karyawan --}}
    <div
        x-show="profilModalOpen"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        @click.self="closeProfilModal"
    >
        {{-- bagian latar belakang gelap (backdrop) saat modal terbuka --}}
        <div class="absolute inset-0 bg-black/50"></div>

        {{-- bagian kotak utama dari modal --}}
        <div
            x-show="profilModalOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative bg-white rounded-2xl shadow-xl w-full max-w-md z-10"
        >
            {{-- bagian atas (header) dari modal yang berisi judul dan tombol tutup --}}
            <div class="px-6 pt-6 pb-4 border-b border-gray-100">
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Edit Profil Karyawan</h2>
                        <p class="text-sm text-gray-500 mt-0.5" x-text="selectedKaryawan ? selectedKaryawan.nip : ''"></p>
                    </div>
                    <button @click="closeProfilModal" class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- bagian tengah (body) dari modal yang berisi form isian --}}
            <div class="px-6 py-5 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                    <input type="text" x-model="editProfil.nama" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan</label>
                    <input type="text" x-model="editProfil.jabatan" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Unit</label>
                    <input type="text" x-model="editProfil.unit" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent outline-none">
                </div>
            </div>

            {{-- bagian bawah (footer) dari modal --}}
            <div class="px-6 pb-6 flex items-center gap-3 justify-end">
                <button @click="closeProfilModal" class="px-4 py-2 text-sm font-medium text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button @click="saveProfil" class="px-4 py-2 text-sm font-medium text-white bg-teal-600 rounded-lg hover:bg-teal-700 transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125"/>
                    </svg>
                    Simpan Profil
                </button>
            </div>
        </div>
    </div>

    {{-- bagian yang digunakan untuk menampilkan modal tambah pengguna --}}
    <div
        x-show="tambahModalOpen"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        @click.self="closeTambahModal"
    >
        <div class="absolute inset-0 bg-black/50"></div>
        <div
            x-show="tambahModalOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative bg-white rounded-2xl shadow-xl w-full max-w-md z-10"
        >
            <div class="px-6 pt-6 pb-4 border-b border-gray-100">
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Tambah Pengguna</h2>
                        <p class="text-sm text-gray-500 mt-0.5">Tambahkan karyawan baru ke sistem</p>
                    </div>
                    <button @click="closeTambahModal" class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="px-6 py-5 space-y-4 max-h-[60vh] overflow-y-auto">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">NIP <span class="text-red-500">*</span></label>
                    <input type="text" x-model="formTambah.nip" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Username <span class="text-red-500">*</span></label>
                    <input type="text" x-model="formTambah.username" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password <span class="text-red-500">*</span></label>
                    <input type="password" x-model="formTambah.password" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" x-model="formTambah.nama" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan</label>
                    <input type="text" x-model="formTambah.jabatan" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Unit <span class="text-red-500">*</span></label>
                    <select x-model="formTambah.unit" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-transparent outline-none">
                        <option value="">-- Pilih Unit --</option>
                        <option value="Jurusan Teknik Elektro">Jurusan Teknik Elektro</option>
                        <option value="Jurusan Teknik Mesin">Jurusan Teknik Mesin</option>
                        <option value="Jurusan Manajemen dan Bisnis">Jurusan Manajemen dan Bisnis</option>
                        <option value="Jurusan Teknik Informatika">Jurusan Teknik Informatika</option>
                        <option value="Pusat">Pusat</option>
                    </select>
                </div>
            </div>

            <div class="px-6 pb-6 pt-4 flex items-center gap-3 justify-end border-t border-gray-100">
                <button @click="closeTambahModal" class="px-4 py-2 text-sm font-medium text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button @click="saveTambah" class="px-4 py-2 text-sm font-medium text-white bg-sky-600 rounded-lg hover:bg-sky-700 transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    Simpan Pengguna
                </button>
            </div>
        </div>
    </div>

    {{-- bagian yang digunakan untuk menampilkan notifikasi (toast) secara sementara --}}
    <div
        x-show="toast.show"
        x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed bottom-6 right-6 z-50 flex items-center gap-3 bg-gray-900 text-white px-4 py-3 rounded-xl shadow-xl text-sm font-medium"
    >
        <svg class="w-5 h-5 text-green-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span x-text="toast.message"></span>
    </div>

    {{-- bagian skrip utama alpine.js untuk mengontrol state halaman --}}
    {{-- (alur data: variabel $karyawan dan $grupverifikasi yang di-inject di sini dikirim (diambil) dari fungsi index() pada pegawaicontroller di baris 39) --}}
    <script>
        function manajemenKaryawan(initialKaryawan, initialGrupVerifikasi) {
            return {
                search: '',
                filterUnit: '',
                filterRole: '',
                perPage: 10,
                currentPage: 1,

                modalOpen: false,
                selectedKaryawan: null,
                selectedRole: 'staff',
                selectedGrup: '',

                profilModalOpen: false,
                editProfil: {
                    id: null,
                    nama: '',
                    jabatan: '',
                    unit: ''
                },

                tambahModalOpen: false,
                formTambah: {
                    nip: '',
                    username: '',
                    password: '',
                    nama: '',
                    jabatan: '',
                    unit: ''
                },

                toast: { show: false, message: '' },

                grupVerifikasi: initialGrupVerifikasi || [],
                karyawan: initialKaryawan || [],

                get filteredData() {
                    return this.karyawan.filter(k => {
                        const matchSearch = !this.search ||
                            k.nama.toLowerCase().includes(this.search.toLowerCase()) ||
                            k.nip.includes(this.search);
                        const matchUnit = !this.filterUnit || k.unit === this.filterUnit;
                        const matchRole = !this.filterRole || k.role === this.filterRole;
                        return matchSearch && matchUnit && matchRole;
                    });
                },

                get totalPages() {
                    return Math.ceil(this.filteredData.length / this.perPage) || 1;
                },

                get paginatedData() {
                    const start = (this.currentPage - 1) * this.perPage;
                    return this.filteredData.slice(start, start + parseInt(this.perPage));
                },

                prevPage() {
                    if (this.currentPage > 1) this.currentPage--;
                },

                nextPage() {
                    if (this.currentPage < this.totalPages) this.currentPage++;
                },

                openRoleModal(karyawan) {
                    this.selectedKaryawan = karyawan;
                    this.selectedRole = karyawan.role;
                    this.selectedGrup = karyawan.grup_id ?? '';
                    this.modalOpen = true;
                },

                closeModal() {
                    this.modalOpen = false;
                    this.selectedKaryawan = null;
                },

                openProfilModal(karyawan) {
                    this.selectedKaryawan = karyawan;
                    this.editProfil = {
                        id: karyawan.id,
                        nama: karyawan.nama,
                        jabatan: karyawan.jabatan,
                        unit: karyawan.unit
                    };
                    this.profilModalOpen = true;
                },

                closeProfilModal() {
                    this.profilModalOpen = false;
                    this.selectedKaryawan = null;
                },

                openTambahModal() {
                    this.formTambah = { nip: '', username: '', password: '', nama: '', jabatan: '', unit: '' };
                    this.tambahModalOpen = true;
                },

                closeTambahModal() {
                    this.tambahModalOpen = false;
                },

                async saveTambah() {
                    if (!this.formTambah.nip || !this.formTambah.username || !this.formTambah.password || !this.formTambah.nama || !this.formTambah.unit) {
                        this.showToast('Semua kolom yang wajib (*) harus diisi!');
                        return;
                    }

                    try {
                        const res = await fetch(`/setup/manajemen-karyawan`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify(this.formTambah)
                        });
                        
                        if (res.ok) {
                            const data = await res.json();
                            this.karyawan.unshift(data.pengguna);
                            this.closeTambahModal();
                            this.showToast(data.message);
                        } else {
                            const err = await res.json();
                            this.showToast(err.message || 'Gagal menambahkan pengguna');
                        }
                    } catch (e) {
                        this.showToast('Terjadi kesalahan server');
                    }
                },

                async saveProfil() {
                    if (!this.selectedKaryawan) return;
                    try {
                        const res = await fetch(`/setup/manajemen-karyawan/${this.editProfil.id}/profil`, {
                            method: 'PUT',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify(this.editProfil)
                        });
                        
                        if (res.ok) {
                            const idx = this.karyawan.findIndex(k => k.id === this.editProfil.id);
                            if (idx !== -1) {
                                this.karyawan[idx].nama = this.editProfil.nama;
                                this.karyawan[idx].jabatan = this.editProfil.jabatan;
                                this.karyawan[idx].unit = this.editProfil.unit;
                            }
                            this.closeProfilModal();
                            this.showToast('Profil berhasil diperbarui');
                        } else {
                            this.showToast('Gagal memperbarui profil');
                        }
                    } catch (e) {
                        this.showToast('Terjadi kesalahan server');
                    }
                },

                async saveRole() {
                    if (!this.selectedKaryawan) return;

                    if (this.selectedRole === 'verifikator' && !this.selectedGrup) {
                        this.showToast('Pilih grup verifikasi terlebih dahulu!');
                        return;
                    }

                    try {
                        const res = await fetch(`/setup/manajemen-karyawan/${this.selectedKaryawan.id}/peran`, {
                            method: 'PUT',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({ role: this.selectedRole, grup_id: this.selectedGrup })
                        });

                        if (res.ok) {
                            const idx = this.karyawan.findIndex(k => k.id === this.selectedKaryawan.id);
                            if (idx !== -1) {
                                this.karyawan[idx].role = this.selectedRole;
                                this.karyawan[idx].grup_id = this.selectedRole === 'verifikator' ? this.selectedGrup : null;
                            }

                            const grupNama = this.selectedRole === 'verifikator'
                                ? ' · ' + (this.grupVerifikasi.find(g => g.id == this.selectedGrup)?.nama ?? '')
                                : '';
                            const roleLabel = this.selectedRole === 'admin' ? 'Admin' : this.selectedRole === 'verifikator' ? 'Verifikator' : 'Pengguna Biasa';
                            const nama = this.selectedKaryawan.nama;
                            
                            this.closeModal();
                            this.showToast(`Peran ${nama} diperbarui menjadi ${roleLabel}${grupNama}`);
                        } else {
                            this.showToast('Gagal memperbarui peran');
                        }
                    } catch (e) {
                        this.showToast('Terjadi kesalahan server');
                    }
                },

                showToast(message) {
                    this.toast = { show: true, message };
                    setTimeout(() => { this.toast.show = false; }, 3000);
                }
            };
        }
    </script>

        </div>
    </div>
@endsection