<!DOCTYPE html>
@php $user = session('user') ?? ['nama' => 'test account', 'is_admin' => true]; @endphp
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Karyawan — KERNAS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen font-sans" x-data="manajemenKaryawan()">

    {{--NAVBAR --}}
    <nav class="bg-white border-b border-gray-200 sticky top-0 z-40">
        <div class="max-w-screen-xl mx-auto px-4">
            <div class="flex items-center justify-between h-14">
                {{-- ======= --}}
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center">
                        <img src="{{ asset('images/logo_polibatam.png') }}"
                         alt="Logo Polibatam"
                         class="w-14 h-14 object-contain" />
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-800 leading-none">KERNAS</p>
                        <p class="text-xs text-gray-500 leading-none">Kerja elektronik naskah dinas</p>
                    </div>
                </div>

                {{-- profil --}}
                <div class="flex items-center gap-3">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-semibold text-gray-800">{{ $user['nama'] ?? 'Nama Pengguna' }}</p>
                        <p class="text-xs text-gray-500">{{ $user['is_admin'] ? 'Admin' : 'Pegawai' }}</p>
                    </div>
                    <div class="w-9 h-9 rounded-full bg-sky-600 flex items-center justify-center text-white text-sm font-bold">
                        {{ strtoupper(substr($user['nama'] ?? 'U', 0, 2)) }}
                    </div>
                </div>

            </div>

            {{-- link nav --}}
            <div class="hidden md:flex items-center gap-1 border-t border-gray-100">
                    <a href="/dashboard" class="flex items-center gap-1.5 px-3 py-2 text-sm text-gray-600 rounded-md hover:bg-gray-100 hover:text-gray-900 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 9.75L12 3l9 6.75V20.25a.75.75 0 01-.75.75H3.75a.75.75 0 01-.75-.75V9.75z"/>
                        </svg>
                        Home
                    </a>
                    <a href="/surat-keputusan" class="flex items-center gap-1.5 px-3 py-2 text-sm text-gray-600 rounded-md hover:bg-gray-100 hover:text-gray-900 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 9v.906a2.25 2.25 0 01-1.183 1.981l-6.478 3.488M2.25 9v.906a2.25 2.25 0 001.183 1.981l6.478 3.488m8.839 2.51-4.66-2.51m0 0-1.023-.55a2.25 2.25 0 00-2.134 0l-1.022.55m0 0-4.661 2.51m16.5 1.615a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V8.844a2.25 2.25 0 011.183-1.98l7.5-4.04a2.25 2.25 0 012.134 0l7.5 4.04a2.25 2.25 0 011.183 1.98V19.5z"/>
                        </svg>
                        Surat Keputusan
                    </a>
                    <a href="/pengajuan-surat" class="flex items-center gap-1.5 px-3 py-2 text-sm text-gray-600 rounded-md hover:bg-gray-100 hover:text-gray-900 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/>
                        </svg>
                        Pengajuan Surat
                    </a>

                    {{-- Setup Dropdown --}}
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button @click="open = !open" class="flex items-center gap-1.5 px-3 py-2 text-sm text-gray-600 rounded-md hover:bg-gray-100 hover:text-gray-900 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 010 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 010-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Setup
                            <svg class="w-3 h-3 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                            </svg>
                        </button>
                        <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                             class="absolute left-0 mt-1 w-52 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                            <a href="/setup/grup-verifikasi" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/>
                                </svg>
                                Grup Verifikasi
                            </a>
                            <a href="/setup/template-surat" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                                </svg>
                                Template Surat
                            </a>
                            <div class="border-t border-gray-100 my-1"></div>
                            <a href="/penggawai" class="flex items-center gap-2 px-4 py-2 text-sm text-sky-600 font-medium bg-sky-50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                                </svg>
                                Manajemen Karyawan
                            </a>
                        </div>
                    </div>
            </div>

        </div>
    </nav>

    {{-- main stuff here --}}
    <div class="max-w-screen-xl mx-auto px-4 py-8">

        {{-- Header --}}
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Manajemen Karyawan</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola hak akses dan peran karyawan Polibatam</p>
        </div>

        {{-- Toolbar: Search + Filter --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-4 p-4">
            <div class="flex flex-col sm:flex-row gap-3">
                {{-- Search --}}
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

                {{-- Filter search by  Unit --}}
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

                {{-- Filter by  Role --}}
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
        </div>

        {{-- Table --}}
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

                            {{-- Role Badge --}}
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

                            {{-- Atur Role Button --}}
                            <td class="px-4 py-3 text-center">
                                <button
                                    @click="openRoleModal(karyawan)"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg border border-sky-300 text-sky-700 bg-sky-50 hover:bg-sky-100 hover:border-sky-400 transition-colors"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 011.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.559.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.894.149c-.424.07-.764.383-.929.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 01-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.398.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 01-.12-1.45l.527-.737c.25-.35.272-.806.108-1.204-.165-.397-.506-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.108-1.204l-.526-.738a1.125 1.125 0 01.12-1.45l.773-.773a1.125 1.125 0 011.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    Atur Peran
                                </button>
                            </td>
                        </tr>
                    </template>

                    {{-- Empty State --}}
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

            {{-- Pagination --}}
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

    {{-- MOBADL yeah --}}
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
        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/50"></div>

        {{-- Modal Box --}}
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
            {{-- Modal Header --}}
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

            {{-- Modal Body --}}
            <div class="px-6 py-5">

                {{-- ingfo Karyawan --}}
                <div class="bg-gray-50 rounded-lg p-3 mb-5 flex items-center gap-3" x-show="selectedKaryawan">
                    <div class="w-10 h-10 rounded-full bg-sky-100 flex items-center justify-center text-sky-700 font-bold text-sm flex-shrink-0"
                         x-text="selectedKaryawan ? selectedKaryawan.nama.substring(0, 2).toUpperCase() : ''">
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800" x-text="selectedKaryawan ? selectedKaryawan.nama : ''"></p>
                        <p class="text-xs text-gray-500" x-text="selectedKaryawan ? `NIP: ${selectedKaryawan.nip} · ${selectedKaryawan.unit}` : ''"></p>
                    </div>
                </div>

                {{-- Role --}}
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Pilih Peran</p>
                <div class="space-y-2">
                    {{-- Admin --}}
                    <label class="flex items-start gap-3 p-3.5 rounded-xl border-2 cursor-pointer transition-all"
                           :class="selectedRole === 'admin' ? 'border-red-400 bg-red-50' : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50'">
                        <input type="radio" x-model="selectedRole" value="admin" class="mt-0.5 accent-red-500">
                        <div>
                            <p class="text-sm font-semibold text-gray-800">Admin</p>
                            <p class="text-xs text-gray-500 mt-0.5">Akses penuh: buat, edit, publish, dan kelola semua dokumen dan pengguna</p>
                        </div>
                    </label>

                    {{-- Verifikator --}}
                    <div class="rounded-xl border-2 transition-all"
                         :class="selectedRole === 'verifikator' ? 'border-amber-400 bg-amber-50' : 'border-gray-200 hover:border-gray-300'">
                        <label class="flex items-start gap-3 p-3.5 cursor-pointer">
                            <input type="radio" x-model="selectedRole" value="verifikator" class="mt-0.5 accent-amber-500">
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-800">Verifikator</p>
                                <p class="text-xs text-gray-500 mt-0.5">Dapat menyetujui atau menolak pengajuan surat dari staff</p>
                            </div>
                        </label>

                        {{-- Dropdown Grup — hanya muncul saat Verifikator dipilih --}}
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

                    {{-- Pengguna Biasa --}}
                    <label class="flex items-start gap-3 p-3.5 rounded-xl border-2 cursor-pointer transition-all"
                           :class="selectedRole === 'staff' ? 'border-sky-400 bg-sky-50' : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50'">
                        <input type="radio" x-model="selectedRole" value="staff" class="mt-0.5 accent-sky-500">
                        <div>
                            <p class="text-sm font-semibold text-gray-800">Pengguna Biasa</p>
                            <p class="text-xs text-gray-500 mt-0.5">Hanya bisa mengajukan surat dan melihat dokumen yang ditugaskan</p>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Modal Footer (hehe kaki)--}}
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

    {{-- notifikasi sementara --}}
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

    {{-- alpine --}}
    <script>
        function manajemenKaryawan() {
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

                toast: { show: false, message: '' },

                grupVerifikasi: [
                    { id: 1, nama: 'Grup Verifikasi 1' },
                    { id: 2, nama: 'Grup Verifikasi 2' },
                    { id: 3, nama: 'Grup Verifikasi 3' },
                ],

                karyawan: [
                    { nip: '198101092021211003', nama: 'ABDUL ROUF', jabatan: 'Dosen Luar', unit: 'Jurusan Manajemen dan Bisnis', role: 'staff' },
                    { nip: '198101092021211004', nama: 'ABDULLAH SANI', jabatan: 'Dosen', unit: 'Jurusan Teknik Elektro', role: 'verifikator' },
                    { nip: '198906142019031007', nama: 'ABULIA MASKARAI', jabatan: 'Laboran Jurusan', unit: 'Jurusan Teknik Mesin', role: 'staff' },
                    { nip: '199106202019031015', nama: 'ADHE ARYSWAN', jabatan: 'Dosen', unit: 'Jurusan Teknik Mesin', role: 'admin' },
                    { nip: '199106202019031016', nama: 'ADHITOMO WIRAWAN', jabatan: 'Dosen', unit: 'Jurusan Teknik Elektro', role: 'staff' },
                    { nip: '197501012005011001', nama: 'BUDI SANTOSO', jabatan: 'Dosen', unit: 'Jurusan Teknik Informatika', role: 'verifikator' },
                    { nip: '198005152010011002', nama: 'CITRA WULANDARI', jabatan: 'Staf Administrasi', unit: 'Jurusan Manajemen dan Bisnis', role: 'staff' },
                    { nip: '197803202006041001', nama: 'DODI PRASOJO', jabatan: 'Dosen', unit: 'Jurusan Teknik Elektro', role: 'admin' },
                    { nip: '198612182015041003', nama: 'EKA PERMATASARI', jabatan: 'Dosen', unit: 'Jurusan Teknik Mesin', role: 'staff' },
                    { nip: '199203042018031002', nama: 'FAJAR NUGROHO', jabatan: 'Teknisi', unit: 'Jurusan Teknik Informatika', role: 'staff' },
                    { nip: '198407262014042001', nama: 'GITA RAHAYU', jabatan: 'Dosen', unit: 'Jurusan Manajemen dan Bisnis', role: 'verifikator' },
                    { nip: '197609152003121001', nama: 'HENDRA KUSUMA', jabatan: 'Dosen', unit: 'Jurusan Teknik Elektro', role: 'staff' },
                    { nip: '198511092016042002', nama: 'INDAH PERMATA', jabatan: 'Staf Administrasi', unit: 'Jurusan Teknik Mesin', role: 'staff' },
                    { nip: '197412042001121001', nama: 'JOKO WIDODO', jabatan: 'Dosen', unit: 'Jurusan Teknik Informatika', role: 'admin' },
                ],

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
                    return Math.ceil(this.filteredData.length / this.perPage);
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

                saveRole() {
                    if (!this.selectedKaryawan) return;

                    // Validasi: verifikator wajib pilih grup
                    if (this.selectedRole === 'verifikator' && !this.selectedGrup) {
                        this.showToast('⚠ Pilih grup verifikasi terlebih dahulu!');
                        return;
                    }

                    // Update local data
                    const idx = this.karyawan.findIndex(k => k.nip === this.selectedKaryawan.nip);
                    if (idx !== -1) {
                        this.karyawan[idx].role = this.selectedRole;
                        this.karyawan[idx].grup_id = this.selectedRole === 'verifikator' ? this.selectedGrup : null;
                    }

                    // In production, send PATCH request:
                    // fetch(`/setup/manajemen-karyawan/${this.selectedKaryawan.nip}/role`, {
                    //     method: 'PATCH',
                    //     headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Content-Type': 'application/json' },
                    //     body: JSON.stringify({ role: this.selectedRole, grup_id: this.selectedGrup })
                    // });

                    const grupNama = this.selectedRole === 'verifikator'
                        ? ' · ' + (this.grupVerifikasi.find(g => g.id == this.selectedGrup)?.nama ?? '')
                        : '';
                    const roleLabel = this.selectedRole === 'admin' ? 'Admin' : this.selectedRole === 'verifikator' ? 'Verifikator' : 'Pengguna Biasa';
                    const nama = this.selectedKaryawan.nama;
                    this.closeModal();
                    this.showToast(`Peran ${nama} diperbarui menjadi ${roleLabel}${grupNama}`);
                },

                showToast(message) {
                    this.toast = { show: true, message };
                    setTimeout(() => { this.toast.show = false; }, 3000);
                }
            };
        }
    </script>

</body>
</html>