<x-layouts.guest title="Masuk" metaDescription="Masuk ke akun SobatSaldo dan mulai catat keuanganmu.">

    <div class="card p-8 space-y-6">

        {{-- Heading --}}
        <div class="space-y-1">
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-slate-100">Selamat Datang! 👋</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Masuk untuk melanjutkan ke SobatSaldo.</p>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route('login') }}" class="space-y-4" x-data="{ showPass: false, loading: false }" @submit="setTimeout(() => loading = true, 50)">
            @csrf

            @if ($errors->any())
                <div class="p-3 rounded-xl bg-rose-50 text-rose-600 text-sm font-medium">
                    {{ $errors->first() }}
                </div>
            @endif

            {{-- Email --}}
            <div class="space-y-1.5">
                <label for="email" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">
                    Email
                </label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    placeholder="kamu@email.com"
                    autocomplete="email"
                    required
                    class="input-field"
                    value="{{ old('email') }}"
                />
            </div>

            {{-- Password --}}
            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label for="password" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">
                        Password
                    </label>
                    <a href="{{ route('password.request') }}" class="text-xs text-emerald-600 dark:text-emerald-400 hover:underline font-medium">
                        Lupa Password?
                    </a>
                </div>
                <div class="relative">
                    <input
                        id="password"
                        :type="showPass ? 'text' : 'password'"
                        name="password"
                        placeholder="••••••••"
                        autocomplete="current-password"
                        required
                        class="input-field pr-11"
                    />
                    <button
                        type="button"
                        @click="showPass = !showPass"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors"
                        aria-label="Toggle visibility password"
                    >
                        <svg x-show="!showPass" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg x-show="showPass" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Remember Me --}}
            <div class="flex items-center">
                <input id="remember" type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                <label for="remember" class="ml-2 text-sm text-slate-600 dark:text-slate-400">Ingat Saya</label>
            </div>

            {{-- Submit Button --}}
            <button
                id="btn-login"
                type="submit"
                class="btn-primary w-full py-3.5 text-base shadow-lg shadow-emerald-200/60 dark:shadow-emerald-900/40 mt-2"
                :class="{ 'pointer-events-none opacity-75': loading }"
            >
                <svg x-show="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                <span x-text="loading ? 'Memproses...' : 'Masuk'">Masuk</span>
            </button>
        </form>

        {{-- Divider --}}
        <div class="relative flex items-center gap-3">
            <div class="flex-1 h-px bg-slate-100 dark:bg-slate-700"></div>
            <span class="text-xs text-slate-400 dark:text-slate-500 font-medium">atau</span>
            <div class="flex-1 h-px bg-slate-100 dark:bg-slate-700"></div>
        </div>

        {{-- Register Link --}}
        <p class="text-center text-sm text-slate-500 dark:text-slate-400">
            Belum punya akun?
            <a href="{{ route('register') }}" class="text-emerald-600 dark:text-emerald-400 font-semibold hover:underline">
                Daftar Sekarang
            </a>
        </p>
    </div>

</x-layouts.guest>
