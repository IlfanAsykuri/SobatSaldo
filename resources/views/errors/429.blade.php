<x-layouts.guest title="429 — Terlalu Banyak Permintaan">

    <div class="card p-8 space-y-6 text-center">

        {{-- Illustration --}}
        <div class="flex justify-center">
            <div class="relative animate-float">
                <svg viewBox="0 0 160 120" class="w-48 h-36" fill="none" xmlns="http://www.w3.org/2000/svg">
                    {{-- Lightning bolt background --}}
                    <circle cx="80" cy="60" r="45" fill="#fffbeb" stroke="#f59e0b" stroke-width="3"/>
                    {{-- Lightning bolt --}}
                    <path d="M90 25 L65 65 L80 65 L70 95 L105 50 L88 50 Z" fill="#f59e0b" stroke="#d97706" stroke-width="2" stroke-linejoin="round"/>
                    {{-- Speed lines --}}
                    <line x1="25" y1="45" x2="45" y2="45" stroke="#fcd34d" stroke-width="2" stroke-linecap="round"/>
                    <line x1="20" y1="60" x2="40" y2="60" stroke="#fcd34d" stroke-width="2" stroke-linecap="round"/>
                    <line x1="25" y1="75" x2="45" y2="75" stroke="#fcd34d" stroke-width="2" stroke-linecap="round"/>
                    <line x1="115" y1="45" x2="135" y2="45" stroke="#fcd34d" stroke-width="2" stroke-linecap="round"/>
                    <line x1="120" y1="60" x2="140" y2="60" stroke="#fcd34d" stroke-width="2" stroke-linecap="round"/>
                    <line x1="115" y1="75" x2="135" y2="75" stroke="#fcd34d" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>
        </div>

        {{-- Error code badge --}}
        <div class="flex justify-center">
            <span class="badge badge-warning text-base px-4 py-1.5">429</span>
        </div>

        {{-- Text --}}
        <div class="space-y-2">
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-slate-100">
                Slow Down! ⚡
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                Terlalu banyak permintaan dalam waktu singkat. Server kami
                butuh napas sebentar. Coba lagi dalam beberapa momen.
            </p>
        </div>

        @php
            $headers = method_exists($exception, 'getHeaders') ? $exception->getHeaders() : [];
            $retryAfter = $headers['Retry-After'] ?? 60;
        @endphp
        {{-- Countdown --}}
        <div x-data="{ 
                sec: {{ $retryAfter }}, 
                done: false, 
                timer: null,
                get formattedTime() {
                    if (this.sec >= 60) {
                        let m = Math.floor(this.sec / 60);
                        let s = this.sec % 60;
                        return m + ' menit ' + (s > 0 ? s + ' detik' : '');
                    }
                    return this.sec + ' detik';
                }
             }"
             x-init="timer = setInterval(() => { if (sec > 0) { sec-- } else { done = true; clearInterval(timer); } }, 1000)">
            <div x-show="!done" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-amber-50 dark:bg-amber-900/30">
                <div class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></div>
                <p class="text-sm font-semibold text-amber-700 dark:text-amber-400">
                    Coba lagi dalam <span class="tabular-nums" x-text="formattedTime"></span>...
                </p>
            </div>
            <div x-show="done" x-transition class="flex justify-center">
                <a href="{{ route('app.workspace') }}" id="btn-retry-429" class="btn-primary px-8 py-3">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Coba Lagi Sekarang
                </a>
            </div>
        </div>

        {{-- Back --}}
        <button onclick="history.back()" id="btn-go-back-429"
                class="inline-flex items-center gap-1.5 text-sm text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 font-medium transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali
        </button>
    </div>

</x-layouts.guest>
