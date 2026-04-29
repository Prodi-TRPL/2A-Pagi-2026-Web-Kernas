<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard KERNAS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet" />

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen flex flex-col">

    {{-- NAVBAR --}}
    <nav class="bg-white border-b border-gray-200 px-6 py-4 shadow-sm">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-3 mb-2">
                    <img src="{{ asset('images/logo_polibatam.png') }}"
                         alt="Logo Polibatam"
                         class="w-14 h-14 object-contain" />
                    <span class="text-2xl font-bold text-gray-800 tracking-tight">KERNAS</span>
                </div>
            </div>
            
            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-600 font-medium">Halo, Admin!</span>
                {{-- Tombol Logout (Kembali ke halaman login) --}}
                <a href="/" class="px-4 py-2 text-sm font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">
                    Logout
                </a>
            </div>
        </div>
    </nav>

    {{-- MAIN CONTENT --}}
    <main class="flex-grow max-w-7xl mx-auto w-full px-6 py-8">
        
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Selamat Datang di Dashboard</h1>
            <p class="text-sm text-gray-500 mt-1">Sistem Informasi Pengajuan dan Distribusi SK</p>
        </div>

        {{-- KARTU STATISTIK --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            
            {{-- Card 1: Total SK --}}
            <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Total SK Diajukan</p>
                    <h3 class="text-2xl font-bold text-gray-900 mt-1">128</h3>
                </div>
            </div>

            {{-- Card 2: Proses --}}
            <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-amber-50 flex items-center justify-center text-amber-500">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Sedang Diproses</p>
                    <h3 class="text-2xl font-bold text-gray-900 mt-1">14</h3>
                </div>
            </div>

            {{-- Card 3: Selesai --}}
            <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-green-50 flex items-center justify-center text-green-600">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">SK Selesai / Terdistribusi</p>
                    <h3 class="text-2xl font-bold text-gray-900 mt-1">114</h3>
                </div>
            </div>

        </div>

        {{-- AREA KONTEN TAMBAHAN --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Aktivitas Terbaru</h2>
            <div class="text-center py-10 text-gray-400">
                <p>Belum ada data aktivitas untuk ditampilkan saat ini.</p>
                <p class="text-sm mt-1">Data tabel pengajuan surat bisa dimasukkan ke area ini nanti.</p>
            </div>
        </div>

    </main>

    {{-- FOOTER --}}
    <footer class="bg-white border-t border-gray-200 py-4 mt-auto">
        <p class="text-center text-xs text-gray-400">
            &copy; {{ date('Y') }} Politeknik Negeri Batam. All rights reserved.
        </p>
    </footer>

</body>
</html>