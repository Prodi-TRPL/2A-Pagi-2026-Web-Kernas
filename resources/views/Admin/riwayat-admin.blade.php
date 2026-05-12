<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Riwayat | KERNAS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

</head>
<body class="h-full bg-stone-100">
    <header>
         @include('layouts/nav-admin')
    </header>
    <main class="p-2">
        <div class="px-2">
            <h1 class="text-2xl font-bold text-gray-800 mb-4">Riwayat Dokumen</h1>
            <div class="mb-4">
                <input type="text" placeholder="Cari dokumen..." class="w-full p-2 border border-gray-300 rounded" x-model="search">
            </div>
            

    </main>
</body>
</html>