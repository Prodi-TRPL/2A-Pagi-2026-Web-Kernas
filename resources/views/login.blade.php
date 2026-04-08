<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Aplikasi SK Polibatam</title>
   
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: '#243c5a',
                        'navy-dark': '#162d4a',
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="font-sans">

    {{-- Background --}}
    <div class="fixed inset-0 bg-cover bg-center bg-no-repeat brightness-75"
         style="background-image: url('{{ asset('images/polibatam-bg.jpg') }}')">
    </div>
    <div class="fixed inset-0 bg-black/10"></div>

    {{-- Center wrapper --}}
    <div class="relative z-10 min-h-screen flex items-center justify-center p-4">

        {{-- Card --}}
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md px-10 py-10">

            {{-- Header --}}
            <div class="flex items-center justify-center gap-3 mb-2">
                <img src="{{ asset('images/logo-polibatam.png') }}"
                     alt="Logo Polibatam"
                     class="w-12 h-12 rounded-full object-cover">
                <span class="text-2xl font-bold text-gray-900 tracking-tight">Polibatam</span>
            </div>

            <p class="text-center text-gray-500 text-sm font-medium mb-8">
                Aplikasi Pengajuan dan Distribusi SK
            </p>

            {{-- Form --}}
            <form method="POST" action="{{ route('login.post') }}" id="loginForm" novalidate>
                @csrf

                {{-- Error alert --}}
                @if(session('error'))
                    <div class="flex items-center gap-2 bg-red-50 border border-red-200 text-red-600 text-sm font-medium rounded-lg px-4 py-3 mb-5">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                        {{ session('error') }}
                    </div>
                @endif

                {{-- Username --}}
                <div class="mb-5">
                    <label for="username" class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Username DokPol
                    </label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        value="{{ old('username') }}"
                        placeholder="Masukkan username DokPol"
                        autofocus
                        autocomplete="username"
                        class="w-full px-4 py-2.5 rounded-lg border text-sm text-gray-900 bg-gray-50
                               outline-none transition
                               focus:bg-white focus:border-navy focus:ring-2 focus:ring-navy/10
                               {{ $errors->has('username') ? 'border-red-500 bg-red-50' : 'border-gray-300' }}"
                    >
                    @error('username')
                        <p class="flex items-center gap-1 text-red-500 text-xs font-medium mt-1.5">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="mb-6">
                    <label for="password" class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Password
                    </label>
                    <div class="relative">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Masukkan password"
                            autocomplete="current-password"
                            class="w-full px-4 py-2.5 pr-11 rounded-lg border text-sm text-gray-900 bg-gray-50
                                   outline-none transition
                                   focus:bg-white focus:border-navy focus:ring-2 focus:ring-navy/10
                                   {{ $errors->has('password') ? 'border-red-500 bg-red-50' : 'border-gray-300' }}"
                        >
                        <button type="button" id="togglePw"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-navy transition">
                            <svg id="iconEye" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                 viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            <svg id="iconEyeOff" class="w-5 h-5 hidden" fill="none" stroke="currentColor" stroke-width="2"
                                 viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
                                <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
                                <line x1="1" y1="1" x2="23" y2="23"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="flex items-center gap-1 text-red-500 text-xs font-medium mt-1.5">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Submit --}}
                <button type="submit" id="btnLogin"
                        class="w-full py-3 bg-navy hover:bg-navy-dark text-white text-sm font-bold rounded-lg
                               transition shadow-md hover:shadow-lg active:scale-[0.98] disabled:opacity-60
                               disabled:cursor-not-allowed flex items-center justify-center gap-2">
                    <span id="btnText">Login</span>
                    <svg id="btnSpinner" class="hidden w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                    </svg>
                </button>

            </form>

            {{-- Footer note --}}
            <p class="text-center text-gray-400 text-xs mt-6">
                *Gunakan Login <strong class="text-gray-500">DokPol</strong> Anda
            </p>

        </div>
    </div>

    <script>
        // Toggle password visibility
        const togglePw   = document.getElementById('togglePw');
        const pwInput    = document.getElementById('password');
        const iconEye    = document.getElementById('iconEye');
        const iconEyeOff = document.getElementById('iconEyeOff');

        togglePw.addEventListener('click', () => {
            const isHidden = pwInput.type === 'password';
            pwInput.type = isHidden ? 'text' : 'password';
            iconEye.classList.toggle('hidden', isHidden);
            iconEyeOff.classList.toggle('hidden', !isHidden);
        });

        // Client-side validation + loading state
        const form       = document.getElementById('loginForm');
        const btnLogin   = document.getElementById('btnLogin');
        const btnText    = document.getElementById('btnText');
        const btnSpinner = document.getElementById('btnSpinner');
        const usernameInput = document.getElementById('username');

        form.addEventListener('submit', (e) => {
            document.querySelectorAll('.js-err').forEach(el => el.remove());
            usernameInput.classList.remove('border-red-500', 'bg-red-50');
            pwInput.classList.remove('border-red-500', 'bg-red-50');

            let valid = true;

            if (!usernameInput.value.trim()) {
                valid = false;
                markError(usernameInput.parentElement, usernameInput, 'This field is required');
            }
            if (!pwInput.value) {
                valid = false;
                markError(pwInput.parentElement, pwInput, 'This field is required');
            }

            if (!valid) { e.preventDefault(); return; }

            btnLogin.disabled = true;
            btnText.textContent = 'Memproses...';
            btnSpinner.classList.remove('hidden');
        });

        function markError(wrapper, input, msg) {
            input.classList.add('border-red-500', 'bg-red-50');
            const p = document.createElement('p');
            p.className = 'js-err flex items-center gap-1 text-red-500 text-xs font-medium mt-1.5';
            p.innerHTML = `<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
            </svg>${msg}`;
            wrapper.after(p);
        }
    </script>

</body>
</html>
