<x-layouts.guest title="500 — Server Error">

    <div class="card p-8 space-y-6 text-center">

        {{-- Illustration --}}
        <div class="flex justify-center">
            <div class="relative animate-float">
                <svg viewBox="0 0 160 120" class="w-48 h-36" fill="none" xmlns="http://www.w3.org/2000/svg">
                    {{-- Gear big --}}
                    <g transform="translate(50,40)">
                        <path d="M30 0 L34 10 L44 6 L46 16 L56 18 L52 28 L60 34 L54 42 L58 52 L48 54 L46 64 L36 62 L30 70 L24 62 L14 64 L12 54 L2 52 L6 42 L0 34 L8 28 L4 18 L14 16 L16 6 L26 10 Z" fill="#e2e8f0" stroke="#94a3b8" stroke-width="2" stroke-linejoin="round"/>
                        <circle cx="30" cy="35" r="12" fill="#f8fafc" stroke="#94a3b8" stroke-width="2"/>
                        <text x="23" y="40" font-size="14" fill="#94a3b8">⚙</text>
                    </g>
                    {{-- Gear small (right) --}}
                    <g transform="translate(100,25) scale(0.65)">
                        <path d="M22 0 L25 8 L33 5 L35 13 L43 14 L40 22 L47 27 L43 34 L46 42 L38 44 L37 52 L28 50 L22 57 L16 50 L7 52 L6 44 L-2 42 L1 34 L-3 27 L4 22 L1 14 L9 13 L11 5 L19 8 Z" fill="#ddd6fe" stroke="#a78bfa" stroke-width="2" stroke-linejoin="round"/>
                        <circle cx="22" cy="28" r="9" fill="#f5f3ff" stroke="#a78bfa" stroke-width="2"/>
                    </g>
                    {{-- Wrench --}}
                    <g transform="translate(15,55) rotate(-35)">
                        <rect x="0" y="0" width="8" height="40" rx="4" fill="#10b981" opacity="0.7"/>
                        <rect x="-5" y="0" width="18" height="12" rx="4" fill="#059669"/>
                        <rect x="-5" y="6" width="8" height="8" rx="2" fill="#f8fafc"/>
                    </g>
                    {{-- Exclamation --}}
                    <circle cx="130" cy="25" r="12" fill="#fee2e2" stroke="#f87171" stroke-width="2"/>
                    <text x="126" y="31" font-size="14" fill="#ef4444" font-weight="bold">!</text>
                </svg>
            </div>
        </div>

        {{-- Error code badge --}}
        <div class="flex justify-center">
            <span class="badge badge-danger text-base px-4 py-1.5">500</span>
        </div>

        {{-- Text --}}
        <div class="space-y-2">
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-slate-100">
                Sistem Dalam Perbaikan 🔧
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                Ups! Server kami sedang merapikan catatan keuangan. Tim teknis
                sudah diberitahu. Coba lagi sesaat lagi ya.
            </p>
        </div>

        {{-- Status detail --}}
        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-700/40 text-left space-y-1.5">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Info Teknis</p>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-mono">
                Error: Internal Server Error (500)<br/>
                Time: {{ now()->format('d M Y, H:i') }} WIB<br/>
                ID: SS-{{ strtoupper(substr(md5(now()), 0, 8)) }}
            </p>
        </div>

        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('app.workspace') }}" id="btn-back-home-500" class="btn-primary px-8 py-3">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Kembali ke Beranda
            </a>
            <button onclick="location.reload()" id="btn-reload-500" class="btn-secondary px-8 py-3">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Muat Ulang Halaman
            </button>
        </div>
    </div>

</x-layouts.guest>
