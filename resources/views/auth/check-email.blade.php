<x-layouts.guest title="Cek Email Kamu" metaDescription="Verifikasi alamat email kamu untuk mulai menggunakan SobatSaldo.">



    <h1 class="text-2xl font-bold text-slate-800 dark:text-white text-center mb-2">Cek Inbox Kamu!</h1>
    <p class="text-slate-500 dark:text-slate-400 text-center text-sm mb-6">
        Kami sudah mengirimkan tautan verifikasi ke<br>
        <strong class="text-slate-700 dark:text-slate-300">{{ Auth::user()?->email ?? 'emailmu' }}</strong>
    </p>

    {{-- Info box --}}
    <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-900/30 flex items-start gap-3 mb-6">
        <span class="text-2xl shrink-0">✉️</span>
        <div class="text-sm text-emerald-700 dark:text-emerald-300 leading-relaxed">
            <p class="font-semibold mb-1">Langkah selanjutnya:</p>
            <ol class="list-decimal pl-4 space-y-1">
                <li>Buka email dari <strong>SobatSaldo</strong></li>
                <li>Klik tombol <strong>"Verifikasi Email"</strong></li>
                <li>Kamu akan langsung masuk ke workspace 🎉</li>
            </ol>
        </div>
    </div>

    {{-- Status message --}}
    @if (session('status'))
        <div class="p-3 rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 text-sm font-medium mb-4 text-center">
            {{ session('status') }}
        </div>
    @endif

    {{-- Resend form --}}
    <form method="POST" action="{{ route('verification.send') }}" x-data="{ loading: false }" @submit="setTimeout(() => loading = true, 50)">
        @csrf
        <button
            id="btn-resend-verification"
            type="submit"
            class="btn-primary w-full py-3 text-sm"
            :class="{ 'pointer-events-none opacity-75': loading }"
        >
            <svg x-show="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
            <span x-text="loading ? 'Mengirim ulang...' : '📤 Kirim Ulang Email Verifikasi'">📤 Kirim Ulang Email Verifikasi</span>
        </button>
    </form>

    <div class="flex items-center justify-center mt-5 gap-1 text-sm">
        <span class="text-slate-400">Sudah punya akun?</span>
        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form-check-email').submit();"
           class="text-emerald-600 dark:text-emerald-400 font-semibold hover:underline">
            Ganti akun
        </a>
    </div>
    <form id="logout-form-check-email" method="POST" action="{{ route('logout') }}" class="hidden">@csrf</form>

</x-layouts.guest>
