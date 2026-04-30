<!DOCTYPE html>
@php $user = session('user') ?? ['nama' => 'test account', 'is_admin' => true]; @endphp
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pengajuan SK — KERNAS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen font-sans" x-data="formPengajuan()">

    {{--NAVBAR --}}
    <nav class="bg-white border-b border-gray-200 sticky top-0 z-40">
        <div class="max-w-screen-xl mx-auto px-4">
            <div class="flex items-center justify-between h-14">
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
                <a href="/pengajuan_surat" class="flex items-center gap-1.5 px-3 py-2 text-sm text-sky-600 bg-sky-50 rounded-md font-medium">
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
                        <a href="/penggawai" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                            </svg>
                            Manajemen Karyawan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    {{-- MAIN CONTENT --}}
    <div class="max-w-5xl mx-auto px-4 py-6">
        {{-- Header --}}
        <div class="mb-6">
            <h1 class="text-xl font-bold text-gray-900">Form Pengajuan SK</h1>
            <p class="text-sm text-gray-500 mt-1">Pengajuan Surat Menetapkan pada Politeknik Negeri Batam</p>
        </div>

        {{-- Action Buttons di atas form --}}
        <div class="flex justify-end gap-3 mb-4">
            <button class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Kembali
            </button>
            <button class="flex items-center gap-2 px-4 py-2 text-sm text-white bg-gray-700 rounded-lg hover:bg-gray-800 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z"/>
                </svg>
                Simpan Sebagai Draft
            </button>
            <button class="flex items-center gap-2 px-4 py-2 text-sm text-white bg-sky-600 rounded-lg hover:bg-sky-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/>
                </svg>
                Ajukan
            </button>
        </div>

        {{-- Form Card --}}
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
            <form>
                {{-- Judul Surat dan Jenis Surat --}}
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Judul Surat Menetapkan/Peraturan*</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent" placeholder="Masukkan judul surat">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Surat*</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent bg-white">
                            <option value="">Pilih jenis surat</option>
                            <option value="sk-honor">SK honor</option>
                            <option value="sk-nonhonor">SK Non Honor</option>
                            <option value="perdir">Perdir</option>
                            <option value="sk-penetapan">SK penetapan</option>
                            <option value="sk-pengangkatan">SK pengangkatan</option>
                            <option value="sk-pemberhentian">SK pemberhentian</option>
                            <option value="sk-penugasan">SK penugasan</option>
                            <option value="sk-penugasan">Pembentukan Tim</option>
                        </select>
                    </div>
                </div>

                {{-- Tanggal Tetap SK --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Tetap SK*</label>
                    <input type="date" value="2026-03-06" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent">
                </div>

                {{-- Toggle Lampiran --}}
                <div class="mb-6">
                    <div class="flex items-center gap-3">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" x-model="adaLampiran" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-sky-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-sky-600"></div>
                        </label>
                        <span class="text-sm text-gray-700" x-text="adaLampiran ? 'Apakah ada lampiran? (Ya)' : 'Apakah ada lampiran? (Tidak)'"></span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1 ml-14">Upload lampiran jika ada</p>
                </div>

                <div class="border-t border-gray-200 my-6"></div>

                {{-- Menimbang Section --}}
                <div class="mb-6">
                    <template x-for="(item, index) in menimbangItems" :key="index">
                        <div class="flex items-start gap-3 mb-4">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-2" x-text="`${index + 1}. Menimbang*`"></label>
                                <textarea 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent resize-none" 
                                    rows="3"
                                    placeholder="Masukkan pertimbangan..."
                                ></textarea>
                            </div>
                            <button 
                                type="button"
                                @click="menimbangItems.splice(index, 1)"
                                x-show="menimbangItems.length > 1"
                                class="mt-8 p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded transition-colors"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                </svg>
                            </button>
                        </div>
                    </template>

                    <button 
                        type="button"
                        @click="menimbangItems.push({})"
                        class="flex items-center gap-2 px-3 py-1.5 text-sm text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                        Tambah Menimbang
                    </button>
                </div>

                <div class="border-t border-gray-200 my-6"></div>

                {{-- Mengingat Section --}}
                <div class="mb-6">
                    <template x-for="(item, index) in mengingatItems" :key="index">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2" x-text="`${index + 1}. Mengingat*`"></label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent bg-white">
                                <option value="">Pilih dasar hukum</option>
                                <option value="1">UU No. 1 Tahun 2023</option>
                                <option value="2">PP No. 2 Tahun 2023</option>
                            </select>
                        </div>
                    </template>

                    <button 
                        type="button"
                        @click="mengingatItems.push({})"
                        class="flex items-center gap-2 px-3 py-1.5 text-sm text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                        Tambah Mengingat
                    </button>
                </div>

                <div class="border-t border-gray-200 my-6"></div>

                {{-- Menetapkan Section --}}
                <div class="mb-6">
                    <template x-for="(item, index) in menetapkanItems" :key="index">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2" x-text="`${index + 1}. Menetapkan*`"></label>
                            <textarea 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent resize-none" 
                                rows="3"
                                placeholder="Masukkan ketetapan..."
                            ></textarea>
                        </div>
                    </template>

                    <button 
                        type="button"
                        @click="menetapkanItems.push({})"
                        class="flex items-center gap-2 px-3 py-1.5 text-sm text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                        Tambah Menetapkan
                    </button>
                </div>

                <div class="border-t border-gray-200 my-6"></div>

                {{-- Pilih Pegawai --}}
                <div class="border border-gray-200 rounded-lg mb-4">
                    <button 
                        type="button"
                        @click="showPegawai = !showPegawai"
                        class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors"
                    >
                        <span x-text="`Pilih Pegawai (${selectedPegawai.length})`"></span>
                        <svg class="w-5 h-5 transition-transform" :class="showPegawai ? 'rotate-90' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                        </svg>
                    </button>
                    <div x-show="showPegawai" x-cloak class="px-4 pb-4 border-t border-gray-200">
                        <p class="text-sm text-gray-500 py-3">Pilih pegawai yang terkait dengan surat ini</p>
                    </div>
                </div>

                {{-- Pilih Unit --}}
                <div class="border border-gray-200 rounded-lg mb-4">
                    <button 
                        type="button"
                        @click="showUnit = !showUnit"
                        class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors"
                    >
                        <span x-text="`Pilih Unit (${selectedUnit.length})`"></span>
                        <svg class="w-5 h-5 transition-transform" :class="showUnit ? 'rotate-90' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                        </svg>
                    </button>
                    <div x-show="showUnit" x-cloak class="px-4 pb-4 border-t border-gray-200">
                        <p class="text-sm text-gray-500 py-3">Pilih unit yang terkait dengan surat ini</p>
                    </div>
                </div>

                {{-- Pilih Group --}}
                <div class="border border-gray-200 rounded-lg">
                    <button 
                        type="button"
                        @click="showGroup = !showGroup"
                        class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors"
                    >
                        <span x-text="`Pilih Group (${selectedGroup.length})`"></span>
                        <svg class="w-5 h-5 transition-transform" :class="showGroup ? 'rotate-90' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                        </svg>
                    </button>
                    <div x-show="showGroup" x-cloak class="px-4 pb-4 border-t border-gray-200">
                        <p class="text-sm text-gray-500 py-3">Pilih group yang terkait dengan surat ini</p>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function formPengajuan() {
            return {
                adaLampiran: false,
                menimbangItems: [{}, {}],
                mengingatItems: [{}],
                menetapkanItems: [{}],
                showPegawai: false,
                showUnit: false,
                showGroup: false,
                selectedPegawai: [],
                selectedUnit: [],
                selectedGroup: []
            };
        }
    </script>

</body>
</html>