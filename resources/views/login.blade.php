<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login — kernas Polibatam</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo_polibatam.png') }}">

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- bagian tag yang digunakan untuk memuat font eksternal agar antarmuka terlihat lebih menarik --}}
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet" />

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }

        .card-shadow {
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25), 0 4px 16px rgba(0, 0, 0, 0.15);
        }

        /* Hilangkan ikon mata (reveal) bawaan dari browser Microsoft Edge (i hate Edge) */
        input[type="password"]::-ms-reveal,
        input[type="password"]::-ms-clear {
            display: none;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #1e3a6e;
            box-shadow: 0 0 0 3px rgba(30, 58, 110, 0.1);
        }

        .btn-login {
            background: #1e3a6e;
            transition: background 0.2s ease, transform 0.1s ease;
        }
        .btn-login:hover { background: #162d56; }
        .btn-login:active { transform: scale(0.99); background: #0f2040; }
        .btn-login:disabled { background: #8fa3c4; cursor: not-allowed; }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-up {
            animation: fadeUp 0.45s ease both;
        }
    </style>
</head>

<body class="relative min-h-screen flex items-center justify-center overflow-hidden px-4 py-10 bg-cover bg-center bg-no-repeat"
      style="background-image: url('{{ asset('images/login_poltek.jpg') }}');">

    <div class="absolute inset-0 bg-black/40"></div>

    {{-- bagian kontainer utama (kartu) yang digunakan untuk menampung formulir login --}}
    <div class="relative z-10 w-full max-w-md animate-fade-up"
         x-data="{
            showPassword: false,
         }">

        <div class="bg-white rounded-2xl card-shadow px-8 py-10">
            <div class="flex flex-col items-center mb-6">
                <div class="flex items-center gap-3 mb-3">
                    <img src="{{ asset('images/logo_polibatam.png') }}"
                         alt="Logo Polibatam"
                         class="w-14 h-14 object-contain" />
                    <span class="text-2xl font-bold text-gray-800 tracking-tight">KERNAS</span>
                </div>
                <p class="text-sm text-gray-500 text-center leading-snug">
                    Kerja Elektronik Naskah Dinas
                </p>
            </div>

            @if (session('error'))
                <div class="mb-5 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-600 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            {{-- (alur data: form ini mengirim inputan 'username' dan 'password' ke fungsi login() pada authcontroller di baris 21) --}}
            <form method="POST" action="{{ route('login.post') }}">
                @csrf
                {{-- bagian input form yang digunakan untuk memasukkan username --}}
                <div class="mb-5">
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-1.5">Username</label>
                    <input type="text" id="username" name="username" value="{{ old('username') }}"
                        class="w-full h-11 px-3.5 rounded-lg border text-sm text-gray-800 transition-all duration-150 @error('username') border-red-400 bg-red-50 @else border-gray-300 bg-white @enderror"
                        autocomplete="username" placeholder="" required />
                    @error('username')
                        <p class="mt-1.5 text-xs text-red-500 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- bagian input form yang digunakan untuk memasukkan kata sandi (password) --}}
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
                    <div class="relative">
                        <input :type="showPassword ? 'text' : 'password'" id="password" name="password"
                            class="w-full h-11 px-3.5 pr-11 rounded-lg border text-sm text-gray-800 transition-all duration-150 @error('password') border-red-400 bg-red-50 @else border-gray-300 bg-white @enderror"
                            autocomplete="current-password" required />
                        <button type="button" @click="showPassword = !showPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                            <svg x-show="!showPassword" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                            </svg>
                            <svg x-show="showPassword" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-xs text-red-500 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- bagian tombol utama yang digunakan untuk mengeksekusi aksi login --}}
                <button type="submit"
                    class="btn-login w-full h-11 rounded-lg text-white font-semibold text-sm tracking-wide flex items-center justify-center gap-2">
                    <span>Login</span>
                </button>
            </form>

            <p class="mt-5 text-center text-xs text-gray-400">
                *Gunakan Login <span class="font-semibold text-gray-600"></span> Anda
            </p>
        </div>

    </div>
</body>
</html>