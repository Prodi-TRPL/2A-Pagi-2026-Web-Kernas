<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dashboard | KERNAS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .poppins{
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="h-full bg-stone-100">
<<<<<<< HEAD
    <div class="flex justify-between bg-white items-center px-2 py-2 border-b-2 border-b-stone-200">
    <img class="h-10 w-auto px-2" src="https://www.polibatam.ac.id/wp-content/uploads/2024/01/cropped-cropped-cropped-02_Logo_1_Utama_Polibatam_Horizontal@2x.png" alt="Logo Polibatam">
        <div class="flex gap-2">
            <div class="flex flex-col justify-center">
                <span class="text-[12px] leading-tight">something</span>
                <span class="text-[10px] leading-tight text-right">Pegawai</span>
            </div>
            <div class="px-2" x-data="{ open: false }">
                <button @click="open = !open"><img class="h-8 w-8 rounded-full" src="https://w7.pngwing.com/pngs/184/113/png-transparent-user-profile-computer-icons-profile-heroes-black-silhouette-thumbnail.png" alt="profile"></button>
                <div x-show="open" @click.outside="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg border border-stone-200">
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Settings</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</a>
                </div>
            </div>
        </div>
    </div>
    <nav>
        <div class="flex bg-white rounded-b-sm font-medium items-center gap-6 px-4 py-2 border-b border-b-stone-200">
            <a class="pb-1 border-b-2 border-blue-800" href="#">Dashboard</a>
            <a class="pb-1 border-b-2 border-transparent hover:border-blue-800" href="#">Riwayat</a>
            <a class="pb-1 border-b-2 border-transparent hover:border-blue-800" href="#">Layanan</a>
            <div x-data="{open: false}" class="relative">
                <button @click="open = !open" class="pb-1 border-b-2 border-transparent hover:border-blue-800" href="#">Setup</button>
                <div x-show="open" @click.outside="open = false" class="absolute  left-1/2 -translate-x-1/2 w-32 bg-white border border-stone-200 rounded-md">
                    <a href="#" class="block px-2 py-2 text-sm text-gray-700 hover:bg-gray-100">Kelola Pengguna</a>
                    <a href="#" class="block px-2 py-2 text-sm text-gray-700 hover:bg-gray-100">Kelola dokumen</a>
                </div>
            </div>
            <a class="pb-1 border-b-2 border-transparent hover:border-blue-800" href="#">UU RI</a>
        </div>
    </nav>
=======
    <header>
         @include('layouts/nav-admin')
    </header>
>>>>>>> ccf986bcc97c50741672ffd0928130849a572028
    <main class="p-2">
            <div class="px-2">
                <h1 class="text-2xl font-bold text-gray-900">Selamat Datang di Dashboard</h1>
                <div class="flex justify-between text-sm text-gray-500 mt-1">Sistem Informasi Pengajuan dan Distribusi Surat
                    <span class="gap-x-2 px-2 poppins font-medium">
                        <span class="text-md rounded">Range:</span>
                        <select class="text-md bg-white border-2 border-black rounded shadow-lg hover:bg-gray-400 cursor-pointer" id="year" name="year">
                        <option value="all">Year</option>
                        <option value="2026">2026</option>
                        <option value="2025">2025</option>
                        <option value="2024">2024</option>
                        </select>
                        <select class="text-md bg-white border-2 border-black rounded shadow-lg hover:bg-gray-400 cursor-pointer" id="month" name="month">
                        <option value="all">Month</option>
                        <option value="2026">Jan</option>
                        <option value="2025">Feb</option>
                        <option value="2024">Mar</option>
                        <option value="2026">Apr</option>
                        <option value="2025">May</option>
                        <option value="2024">Jun</option>
                        <option value="2026">Jul</option>
                        <option value="2025">Aug</option>
                        <option value="2024">Sep</option>
                        <option value="2026">Oct</option>
                        <option value="2025">Nov</option>
                        <option value="2024">Des</option>
                        </select>
                    </span>
                </div>
            </div>
        <div class="flex flex-row gap-4 items-center bg-stone-100 p-2 rounded-lg">
            <div class="basis-full h-20 bg-white rounded-lg shadow-md p-4 flex items-center gap-1">
                <i class="fa-solid fa-file-invoice text-xl" style="color: rgb(255, 212, 59);"></i>
                <div class="flex flex-col justify-center">
                    <p class="text-left text-2xl font-sans font-medium leading-none">10</p>
                    <p class="text-left text-md font-sans font-light">Surat Tugas</p>
                </div>
            </div>
            <div class="basis-full h-20 bg-white rounded-lg shadow-md p-4 flex items-center gap-1">
                <i class="fa-solid fa-file-contract text-xl" style="color: rgb(255, 212, 59);"></i>
                <div class="flex flex-col justify-center">
                    <p class="text-left text-2xl font-sans font-medium leading-none">15</p>
                    <p class="text-left text-md font-sans font-light">Surat Keputusan</p>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-3 gap-4 p-2">
            <div class="lg:col-span-2 bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                <div class="mb-8">
                    <h3 class="text-xl font-bold text-gray-800">Pengajuan Surat</h3>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mt-1">Status Pengajuan</p>
                </div>
                <div class="relative h-[320px] w-full">
                    <canvas id="barChart"></canvas>
                </div>
            </div>
            <div class="lg:col-span-1 bg-white rounded-lg shadow-sm border border-gray-100 p-6 flex flex-col">
                <div class="mb-6">
                    <h3 class="text-xl font-bold text-gray-800">Total Pengajuan</h3>
                </div>
                <div class="relative w-full h-[350px] flex items-center justify-center">
                    <canvas id="donutChart"></canvas>
                    <div class="absolute flex flex-col items-center justify-center">
                        <span class="text-5xl font-black text-gray-800 tracking-tighter">25</span>
                        <span class="text-[10px] uppercase tracking-[0.2em] text-slate-500 font-bold mt-1">Total</span>
                        <span class="text-[10px] uppercase tracking-[0.2em] text-slate-500 font-bold">Pengajuan</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-t-lg border border-stone-200 shadow-md mx-2 p-2">
            <h1 class="font-medium flex justify-between">Dokumen Terbaru
            </h1>
            <p class="text-xs">10 dokumen terakhir politeknik negeri batam</p>
        </div>
        <div x-data="dokSearch()" class="max-full mx-auto">
            <div class="bg-white h-12 flex justify-end items-center border border-stone-200 shadow-md mx-2">
                <div class="px-1">
                    <input type="text" x-model="search" class="w-32 border border-gray-300 rounded-md px-2 focus:ring-1" placeholder="Search">
                </div>
            </div>
            <div class="px-2">
                <table class="table-auto w-full bg-white shadow-md">
                    <thead>
                        <tr class="h-10 text-left border border-stone-200">
                            <th class="px-2">No</th>
                            <th>Nomor Surat</th>
                            <th>Nama Dokumen</th>
                            <th>Tanggal</th>
                            <th>Dokumen</th>
                            <th>Dibuat pada</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <template x-for="(dok,index) in searchedDokumen" :key="dok.name">
                            <tr class="border hover:bg-gray-50 transition">
                                <td class="px-2 text-sm text-gray-500" x-text="index + 1"></td>
                                <td class="h-10 text-sm text-gray-500" x-text="dok.nomor"></td>
                                <td class="h-10 text-sm text-gray-700" x-text="dok.name"></td>
                                <td class="h-10 text-sm text-gray-700" x-text="dok.date"></td>
                                <td class="h-10 text-sm text-blue-700" x-text="dok.document"></td>
                                <td class="h-10 text-sm text-gray-700" x-text="dok.created_at">
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // js search
    function dokSearch() {
        return {
            search: '',
            users: [
                { nomor: 'SK/01/2024', name: 'Surat Keputusan 1', date: '2024-06-01', document: 'SK-001.pdf', created_at: '2024-06-01  10:00' },
                { nomor: 'ST/01/2024', name: 'Surat Tugas 1', date: '2024-06-02', document: 'ST-001.pdf', created_at: '2024-06-02  11:00' },
                { nomor: 'SK/02/2024', name: 'Surat Keputusan 2', date: '2024-06-03', document: 'SK-002.pdf', created_at: '2024-06-03  12:00' },
                { nomor: 'ST/02/2024', name: 'Surat Tugas 2', date: '2024-06-04', document: 'ST-002.pdf', created_at: '2024-06-04  13:00' },
                { nomor: 'SK/03/2024', name: 'Surat Keputusan 3', date: '2024-06-05', document: 'SK-003.pdf', created_at: '2024-06-05  14:00' },
                { nomor: 'ST/03/2024', name: 'Surat Tugas 3', date: '2024-06-06', document: 'ST-003.pdf', created_at: '2024-06-06  15:00' },
                { nomor: 'SK/04/2024', name: 'Surat Keputusan 4', date: '2024-06-07', document: 'SK-004.pdf', created_at: '2024-06-06  16:00>' },
                { nomor: 'ST/04/2024', name: 'Surat Tugas 4', date: '2024-06-08', document: 'ST-004.pdf', created_at: '2024-06-08  17:00' },
                { nomor: 'SK/05/2024', name: 'Surat Keputusan 5', date: '2024-06-09', document: 'SK-005.pdf', created_at: '2024-06-09  18:00' },
                { nomor: 'ST/05/2024', name: 'Surat Tugas 5', date: '2024-06-10', document: 'ST-005.pdf', created_at: '2024-06-10  19:00' }
            ],
            get searchedDokumen() {
                if (!this.search) return this.users;
                const searchTerm = this.search.toLowerCase();
                return this.users.filter(dok => {
                    return dok.name.toLowerCase().includes(searchTerm) || dok.nomor.toLowerCase().includes(searchTerm);
                });
            }
        }
    }
    // warna tabel
    const colors = {
        diproses: '#FACC15',     // Kuning
        menunggu: '#F97316',    // Oranye
        revisi: '#22C55E',  // Hijau
        diterbitkan: '#1D4ED8', // Biru
    };
    //conf legend
    const commonLegend = {
        position: 'bottom',
        labels: {
            usePointStyle: true,
            padding: 20,
            font: { size: 11, weight: '700' },
            color: '#475569'
        }
    };

    // diagram batang
    new Chart(document.getElementById('barChart'), {
        type: 'bar',
        data: {
            labels: ['JANUARY', 'FEBRUARY', 'MARCH', 'APRIL', 'MAY', 'JUNE', 'JULY'],
            datasets: [
                { label: 'Diproses', data: [0, 5, 0, 0, 0, 0, 0], backgroundColor: colors.diproses, stack: 's1', barPercentage: 0.3 },
                { label: 'Menunggu Verifikasi', data: [0, 0, 0, 0, 0, 0, 0], backgroundColor: colors.menunggu, stack: 's1', barPercentage: 0.3 },
                { label: 'Revisi', data: [0, 0, 0, 0, 0, 0, 0], backgroundColor: colors.revisi, stack: 's1', barPercentage: 0.3 },
                { label: 'Diterbitkan', data: [5, 11, 6, 2, 0, 0, 0], backgroundColor: colors.diterbitkan, stack: 's1', barPercentage: 0.3 }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { ...commonLegend, labels: { ...commonLegend.labels, pointStyle: 'rectRounded' } }
            },
            scales: {
                x: { stacked: true, grid: { display: false }, border: { display: false } },
                y: {
                    stacked: true,
                    beginAtZero: true,
                    max: 20,
                    ticks: { stepSize: 2, color: '#94a3b8' },
                    grid: { color: '#f1f5f9' },
                    border: { display: false }
                }
            }
        }
    });

    // diagram donat
    new Chart(document.getElementById('donutChart'), {
        type: 'doughnut',
        data: {
            labels: ['Surat Tugas', 'Surat Keputusan'],
            datasets: [{
                data: [15, 10],
                backgroundColor: Object.values(colors),
                borderWidth: 0,
                cutout: '80%',
                spacing: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false, // UBAH KE FALSE
            layout: {
                padding: {
                    bottom: 20 // Beri ruang nafas di bawah legend
                }
            },
            plugins: {
                legend: {
                    ...commonLegend,
                    labels: { ...commonLegend.labels, pointStyle: 'circle' }
                }
            }
        }
    });
</script>
</body>
</html>
