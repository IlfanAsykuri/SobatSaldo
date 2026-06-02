<x-layouts.guest title="Verifikasi Email" metaDescription="Verifikasi email akun SobatSaldo kamu.">

    <div class="card p-8 space-y-6 text-center">

        {{-- Animated Envelope Illustration --}}
        <div class="flex justify-center">
            <div class="relative w-24 h-24 animate-float">
                <svg viewBox="0 0 100 100" class="w-full h-full" fill="none" xmlns="http://www.w3.org/2000/svg">
                    {{-- Envelope body --}}
                    <rect x="8" y="28" width="84" height="56" rx="10" fill="#ecfdf5" stroke="#059669" stroke-width="3"/>
                    {{-- Envelope flap --}}
                    <path d="M8 38 L50 62 L92 38" stroke="#059669" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                    {{-- Stars around envelope --}}
                    <text x="2" y="22" font-size="12" fill="#34d399">✦</text>
                    <text x="80" y="18" font-size="10" fill="#10b981">✦</text>
                    <text x="88" y="72" font-size="8" fill="#6ee7b7">✦</text>
                </svg>
            </div>
        </div>

        {{-- Heading --}}
        <div class="space-y-2">
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-slate-100">Cek Emailmu! 📬</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                Kami telah mengirimkan instruksi verifikasi ke:
            </p>
            <p class="text-sm font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/30 px-4 py-2 rounded-2xl inline-block">
                {{ Auth::user()?->email ?? 'emailmu' }}
            </p>
        </div>

        {{-- Steps --}}
        <div class="text-left space-y-3 py-2">
            @foreach ([
                ['icon' => '1', 'text' => 'Buka inbox emailmu'],
                ['icon' => '2', 'text' => 'Klik tombol "Verifikasi Email" di dalam email'],
                ['icon' => '3', 'text' => 'Kamu akan langsung masuk ke SobatSaldo!'],
            ] as $step)
            <div class="flex items-center gap-3">
                <div class="w-7 h-7 rounded-full bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400 flex items-center justify-center text-xs font-bold shrink-0">
                    {{ $step['icon'] }}
                </div>
                <p class="text-sm text-slate-600 dark:text-slate-400">{{ $step['text'] }}</p>
            </div>
            @endforeach
        </div>

        {{-- Status Message --}}
        @if (session('status'))
        <div class="p-3 rounded-2xl bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 text-sm font-medium">
            {{ session('status') }}
        </div>
        @endif

        {{-- Resend form --}}
        <form method="POST" action="{{ route('verification.send') }}"
              x-data="{ loading: false }"
              @submit="setTimeout(() => loading = true, 50)">
            @csrf
            <button
                id="btn-resend-email"
                type="submit"
                class="btn-primary w-full py-3 text-sm"
                :class="{ 'pointer-events-none opacity-75': loading }"
            >
                <svg x-show="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                <span x-text="loading ? 'Mengirim...' : '📤 Kirim Ulang Email Verifikasi'">📤 Kirim Ulang Email Verifikasi</span>
            </button>
        </form>

        {{-- Back link --}}
        <a href="{{ route('login') }}"
           class="inline-flex items-center gap-1.5 text-sm text-slate-400 dark:text-slate-500 hover:text-emerald-600 dark:hover:text-emerald-400 font-medium transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali ke Login
        </a>
    </div>

</x-layouts.guest>
