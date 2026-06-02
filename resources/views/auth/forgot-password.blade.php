<x-layouts.guest title="Lupa Password" metaDescription="Reset password akun SobatSaldo kamu.">

    <div class="card p-8 space-y-6">

        {{-- Icon --}}
        <div class="flex justify-center">
            <div class="w-16 h-16 rounded-2xl bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center animate-float">
                <span class="text-3xl">🔑</span>
            </div>
        </div>

        {{-- Heading --}}
        <div class="text-center space-y-2">
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-slate-100">Lupa Password?</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                Masukkan emailmu dan kami akan mengirimkan<br/>link untuk reset password.
            </p>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route('password.email') }}" class="space-y-4" x-data="{ loading: false, sent: {{ session('status') ? 'true' : 'false' }} }" @submit="setTimeout(() => loading = true, 50)">
            @csrf

            @if ($errors->any())
                <div class="p-3 rounded-xl bg-rose-50 text-rose-600 text-sm font-medium">
                    {{ $errors->first() }}
                </div>
            @endif

            {{-- Email --}}
            <div class="space-y-1.5">
                <label for="email" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">
                    Alamat Email
                </label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    placeholder="kamu@email.com"
                    autocomplete="email"
                    required
                    :disabled="sent"
                    class="input-field"
                    value="{{ old('email') }}"
                />
            </div>

            {{-- Success State --}}
            <div x-show="sent" x-transition class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-900/30 flex items-start gap-3">
                <span class="text-lg shrink-0">✅</span>
                <p class="text-sm text-emerald-700 dark:text-emerald-300 font-medium leading-relaxed">
                    {{ session('status') ?? 'Link reset telah dikirim! Cek inbox emailmu (dan folder spam).' }}
                </p>
            </div>

            {{-- Submit --}}
            <button
                id="btn-forgot"
                type="submit"
                x-show="!sent"
                class="btn-primary w-full py-3.5 text-base shadow-lg shadow-emerald-200/60 dark:shadow-emerald-900/40"
                :class="{ 'pointer-events-none opacity-75': loading }"
            >
                <svg x-show="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                <span x-text="loading ? 'Mengirim...' : 'Kirim Link Reset'">Kirim Link Reset</span>
            </button>
        </form>

        {{-- Back to Login --}}
        <div class="text-center">
            <a href="{{ route('login') }}"
               class="inline-flex items-center gap-1.5 text-sm text-slate-500 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali ke Login
            </a>
        </div>
    </div>

</x-layouts.guest>
