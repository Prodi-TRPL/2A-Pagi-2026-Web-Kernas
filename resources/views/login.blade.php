<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login — DokPol Polibatam</title>

    {{-- Tailwind --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet" />

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }

        .card-shadow {
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25), 0 4px 16px rgba(0, 0, 0, 0.15);
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

        /* Fade-in animation for the card */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-up {
            animation: fadeUp 0.45s ease both;
        }

    </style>
</head>

{{-- 
    Ganti nama file di bawah sesuai nama gambar Anda.
    Contoh: asset('images/bg_login.jpg') atau asset('images/bg_login.png')
--}}
<body class="relative min-h-screen flex items-center justify-center overflow-hidden px-4 py-10 bg-cover bg-center bg-no-repeat"
      style="background-image: url('{{ asset('images/login_poltek.jpg') }}');">

    {{-- Overlay gelap agar card tetap terbaca --}}
    <div class="absolute inset-0 bg-black/40"></div>

    {{-- LOGIN CARD --}}
    <div class="relative z-10 w-full max-w-md animate-fade-up"
         x-data="{
            username: '',
            password: '',
            showPassword: false,
            usernameError: '',
            passwordError: '',
            generalError: '',
            loading: false,

            validate() {
                this.usernameError = '';
                this.passwordError = '';
                this.generalError = '';
                let ok = true;
                if (!this.username.trim()) {
                    this.usernameError = 'This field is required';
                    ok = false;
                }
                if (!this.password) {
                    this.passwordError = 'This field is required';
                    ok = false;
                }
                return ok;
            },

            async submit() {
                if (!this.validate()) return;
                this.loading = true;
                {{-- 
                    TODO (backend): ganti simulasi ini dengan form submit biasa ke POST /login
                    <form method='POST' action='{{ route('login') }}'>
                        @csrf
                        ...
                    </form>
                    
                    Untuk sekarang (frontend-only), kita simulasikan saja.
                --}}
                await new Promise(r => setTimeout(r, 1000));
                this.loading = false;
                this.generalError = 'Username atau password salah. Pastikan menggunakan akun DokPol Anda.';
            }
         }">

        <div class="bg-white rounded-2xl card-shadow px-8 py-10">
            <div class="flex flex-col items-center mb-6">
                <div class="flex items-center gap-3 mb-3">
                    <img src="{{ asset('images/logo_polibatam.png') }}"
                         alt="Logo Polibatam"
                         class="w-14 h-14 object-contain" />
                    <span class="text-2xl font-bold text-gray-800 tracking-tight">Polibatam</span>
                </div>
                <p class="text-sm text-gray-500 text-center leading-snug">
                    Aplikasi Pengajuan dan Distribusi SK
                </p>
            </div>

            {{-- ── GENERAL ERROR (dari server / simulasi) ── --}}
            {{-- Error dari session Laravel (backend nanti): --}}
            @if (session('error'))
                <div class="mb-5 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-600 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Error dari Alpine (frontend simulasi): --}}
            <div x-show="generalError"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="mb-5 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-600 text-sm"
                 x-text="generalError">
            </div>

            {{-- ── FORM ── --}}
            {{-- 
                TODO (backend): uncomment tag <form> ini dan hapus @submit.prevent saat backend siap
                <form method="POST" action="{{ route('login') }}">
                    @csrf
            --}}
            <div @submit.prevent="submit">

                {{-- CSRF (aktifkan saat backend siap) --}}
                {{-- @csrf --}}

                {{-- Username --}}
                <div class="mb-5">
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Username DokPol
                    </label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        x-model="username"
                        @input="usernameError = ''"
                        :class="usernameError ? 'border-red-400 bg-red-50' : 'border-gray-300 bg-white'"
                        class="w-full h-11 px-3.5 rounded-lg border text-sm text-gray-800 transition-all duration-150"
                        autocomplete="username"
                        placeholder=""
                    />
                    <p x-show="usernameError"
                       x-text="usernameError"
                       class="mt-1.5 text-xs text-red-500 font-medium">
                    </p>
                </div>

                {{-- Password --}}
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Password
                    </label>
                    <div class="relative">
                        <input
                            :type="showPassword ? 'text' : 'password'"
                            id="password"
                            name="password"
                            x-model="password"
                            @input="passwordError = ''"
                            :class="passwordError ? 'border-red-400 bg-red-50' : 'border-gray-300 bg-white'"
                            class="w-full h-11 px-3.5 pr-11 rounded-lg border text-sm text-gray-800 transition-all duration-150"
                            autocomplete="current-password"
                        />
                        {{-- Toggle show/hide password --}}
                        <button
                            type="button"
                            @click="showPassword = !showPassword"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors"
                            :aria-label="showPassword ? 'Sembunyikan password' : 'Tampilkan password'">
                            {{-- Eye icon --}}
                            <svg x-show="!showPassword" width="18" height="18" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            {{-- Eye-off icon --}}
                            <svg x-show="showPassword" width="18" height="18" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                                <line x1="1" y1="1" x2="23" y2="23"/>
                            </svg>
                        </button>
                    </div>
                    <p x-show="passwordError"
                       x-text="passwordError"
                       class="mt-1.5 text-xs text-red-500 font-medium">
                    </p>
                </div>

                {{-- Submit button --}}
                <button
                    type="button"
                    @click="submit()"
                    :disabled="loading"
                    class="btn-login w-full h-11 rounded-lg text-white font-semibold text-sm tracking-wide flex items-center justify-center gap-2">
                    <svg x-show="loading" class="animate-spin" width="16" height="16" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
                    </svg>
                    <span x-text="loading ? 'Memproses...' : 'Login'">Login</span>
                </button>

            </div>
            {{-- </form> --}}

            {{-- ── HINT ── --}}
            <p class="mt-5 text-center text-xs text-gray-400">
                *Gunakan Login <span class="font-semibold text-gray-600">DokPol</span> Anda
            </p>

        </div>{{-- end card --}}

        {{-- Footer kecil di bawah card --}}
        <p class="text-center text-xs text-white/40 mt-5">
            &copy; {{ date('Y') }} Politeknik Negeri Batam
        </p>

    </div>{{-- end card wrapper --}}

</body>
</html>