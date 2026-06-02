<x-layouts.guest title="404 — Halaman Tidak Ditemukan">

    <div class="card p-8 space-y-6 text-center">

        {{-- Illustration --}}
        <div class="flex justify-center">
            <div class="relative animate-float">
                <svg viewBox="0 0 160 120" class="w-48 h-36" fill="none" xmlns="http://www.w3.org/2000/svg">
                    {{-- Empty wallet body --}}
                    <rect x="20" y="35" width="120" height="75" rx="16" fill="#ecfdf5" stroke="#059669" stroke-width="3"/>
                    {{-- Wallet clasp --}}
                    <rect x="108" y="55" width="28" height="28" rx="8" fill="#d1fae5" stroke="#059669" stroke-width="2"/>
                    <circle cx="122" cy="69" r="5" fill="#059669"/>
                    {{-- "Empty" fold line --}}
                    <line x1="40" y1="60" x2="95" y2="60" stroke="#a7f3d0" stroke-width="2" stroke-linecap="round"/>
                    <line x1="40" y1="72" x2="80" y2="72" stroke="#a7f3d0" stroke-width="2" stroke-linecap="round"/>
                    {{-- Floating coins --}}
                    <circle cx="55" cy="22" r="12" fill="#fef3c7" stroke="#f59e0b" stroke-width="2"/>
                    <text x="49" y="27" font-size="10" fill="#d97706" font-weight="bold">Rp</text>
                    <circle cx="108" cy="16" r="8" fill="#fef3c7" stroke="#f59e0b" stroke-width="1.5"/>
                    <text x="104" y="21" font-size="7" fill="#d97706" font-weight="bold">Rp</text>
                    {{-- Question mark --}}
                    <text x="60" y="100" font-size="32" fill="#059669" font-weight="bold" opacity="0.15">?</text>
                </svg>
            </div>
        </div>

        {{-- Error code badge --}}
        <div class="flex justify-center">
            <span class="badge badge-warning text-base px-4 py-1.5">404</span>
        </div>

        {{-- Text --}}
        <div class="space-y-2">
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-slate-100">
                Waduh, Halaman Kesasar! 🗺️
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                Halaman yang kamu cari tidak ditemukan. Mungkin sudah dipindahkan,
                dihapus, atau kamu salah ketik URL.
            </p>
        </div>

        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('app.workspace') }}" id="btn-back-home-404" class="btn-primary px-8 py-3">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Kembali ke Beranda
            </a>
            <button onclick="history.back()" id="btn-go-back-404" class="btn-secondary px-8 py-3">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
                Halaman Sebelumnya
            </button>
        </div>
    </div>

</x-layouts.guest>
