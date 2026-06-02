<x-layouts.app title="Pengaturan" metaDescription="Kelola profil, keamanan, dan preferensi akunmu.">

{{-- Flash Messages --}}
@if (session('success'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
     x-transition class="fixed top-4 right-4 z-50 p-4 rounded-2xl bg-emerald-500 text-white shadow-xl max-w-sm text-sm font-semibold flex items-center gap-2">
    ✅ {{ session('success') }}
</div>
@endif
@if (session('error'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
     x-transition class="fixed top-4 right-4 z-50 p-4 rounded-2xl bg-rose-500 text-white shadow-xl max-w-sm text-sm font-semibold flex items-center gap-2">
    ⚠️ {{ session('error') }}
</div>
@endif

<div class="max-w-xl mx-auto px-4 py-6 lg:py-8 pb-24 lg:pb-8 space-y-6">

    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Pengaturan ⚙️</h1>
        <p class="text-sm text-slate-400 mt-0.5">Kelola akun dan preferensimu</p>
    </div>

    {{-- ─ Profil ─────────────────────────────────────────────────────────────── --}}
    <div class="card p-5">
        <p class="section-title mb-4">👤 Profil Akun</p>

        {{-- Avatar Inisial --}}
        <div class="flex items-center gap-4 mb-5">
            <div class="w-16 h-16 rounded-3xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-2xl font-bold text-white shadow-lg shadow-emerald-200/50 dark:shadow-emerald-900/40">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <p class="font-bold text-slate-800 dark:text-white">{{ $user->name }}</p>
                <p class="text-sm text-slate-400">{{ $user->email }}</p>
                @if($user->email_verified_at)
                    <span class="text-xs text-emerald-600 dark:text-emerald-400 font-semibold">✅ Email terverifikasi</span>
                @else
                    <span class="text-xs text-amber-500 font-semibold">⚠️ Email belum terverifikasi</span>
                @endif
            </div>
        </div>

        @if ($errors->hasBag('profile') || ($errors->any() && !$errors->hasBag('password')))
        <div class="p-3 mb-3 rounded-xl bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 text-sm">
            @foreach ($errors->all() as $error) <p>• {{ $error }}</p> @endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('settings.profile') }}" class="space-y-3">
            @csrf @method('PUT')
            <div>
                <label class="section-title block mb-1">Nama Panggilan</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="input-field" required />
            </div>
            <div>
                <label class="section-title block mb-1">Alamat Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="input-field" required />
                <p class="text-xs text-slate-400 mt-1">Mengganti email akan membutuhkan verifikasi ulang.</p>
            </div>
            <div>
                <label class="section-title block mb-1">Batas Pengeluaran Harian (Rp)</label>
                <input type="number" name="daily_limit" value="{{ old('daily_limit', $user->daily_limit) }}" class="input-field" min="0" step="1000" />
                <p class="text-xs text-slate-400 mt-1">Kosongkan atau isi 0 jika tidak ingin dibatasi.</p>
            </div>
            <button type="submit" class="btn-primary w-full py-2.5 text-sm">💾 Simpan Profil</button>
        </form>
    </div>

    {{-- ─ Tampilan ───────────────────────────────────────────────────────────── --}}
    <div class="card p-5">
        <p class="section-title mb-4">🎨 Tampilan</p>
        <div class="flex items-center justify-between">
            <div>
                <p class="font-bold text-slate-800 dark:text-white">Mode Gelap (Dark Mode)</p>
                <p class="text-sm text-slate-400">Gunakan tema gelap agar lebih nyaman di mata</p>
            </div>
            <button @click="dark = !dark"
                    class="relative inline-flex h-7 w-12 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900"
                    :class="dark ? 'bg-emerald-500' : 'bg-slate-300 dark:bg-slate-600'">
                <span class="inline-block h-5 w-5 transform rounded-full bg-white transition-transform shadow-sm"
                      :class="dark ? 'translate-x-6' : 'translate-x-1'"></span>
            </button>
        </div>
    </div>

    {{-- ─ Ganti Password ───────────────────────────────────────────────────── --}}
    <div class="card p-5" x-data="{ showCurrent: false, showNew: false, showConfirm: false }">
        <p class="section-title mb-4">🔐 Keamanan & Kata Sandi</p>

        @if ($errors->hasBag('password') || session('password_error'))
        <div class="p-3 mb-3 rounded-xl bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 text-sm">
            @foreach ($errors->all() as $error) <p>• {{ $error }}</p> @endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('settings.password') }}" class="space-y-3">
            @csrf @method('PUT')
            <div>
                <label class="section-title block mb-1">Kata Sandi Saat Ini</label>
                <div class="relative">
                    <input :type="showCurrent ? 'text' : 'password'" name="current_password" class="input-field pr-11" required />
                    <button type="button" @click="showCurrent = !showCurrent"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
                        <svg x-show="!showCurrent" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg x-show="showCurrent" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
            </div>
            <div>
                <label class="section-title block mb-1">Kata Sandi Baru</label>
                <div class="relative">
                    <input :type="showNew ? 'text' : 'password'" name="password" class="input-field pr-11" required placeholder="Min. 8 karakter, huruf & angka" />
                    <button type="button" @click="showNew = !showNew"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
                        <svg x-show="!showNew" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg x-show="showNew" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
            </div>
            <div>
                <label class="section-title block mb-1">Konfirmasi Kata Sandi Baru</label>
                <div class="relative">
                    <input :type="showConfirm ? 'text' : 'password'" name="password_confirmation" class="input-field pr-11" required />
                    <button type="button" @click="showConfirm = !showConfirm"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
                        <svg x-show="!showConfirm" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg x-show="showConfirm" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
            </div>
            <button type="submit" class="btn-primary w-full py-2.5 text-sm">🔒 Ganti Kata Sandi</button>
        </form>
    </div>

    {{-- ─ Ekspor Data ───────────────────────────────────────────────────────── --}}
    <div class="card p-5">
        <p class="section-title mb-2">📥 Ekspor Data Transaksi</p>
        <p class="text-sm text-slate-400 mb-4">Unduh seluruh riwayat transaksimu dalam format yang kamu inginkan.</p>
        <div class="flex gap-3">
            <a href="{{ route('settings.export.csv') }}"
               class="flex-1 flex items-center justify-center gap-2 py-3 rounded-2xl bg-slate-100 dark:bg-slate-700 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 text-slate-700 dark:text-slate-300 hover:text-emerald-700 dark:hover:text-emerald-300 font-semibold text-sm transition-all">
                <span>📄</span> Ekspor CSV
            </a>
            <a href="{{ route('settings.export.excel') }}"
               class="flex-1 flex items-center justify-center gap-2 py-3 rounded-2xl bg-slate-100 dark:bg-slate-700 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 text-slate-700 dark:text-slate-300 hover:text-emerald-700 dark:hover:text-emerald-300 font-semibold text-sm transition-all">
                <span>📊</span> Ekspor Excel
            </a>
        </div>
    </div>

    {{-- ─ PWA Install ───────────────────────────────────────────────────────── --}}
    <div class="card p-5" x-data="{
        deferredPrompt: null,
        canInstall: false,
        installed: false,
        init() {
            if (window.matchMedia('(display-mode: standalone)').matches) {
                this.installed = true;
                return;
            }
            window.addEventListener('beforeinstallprompt', (e) => {
                e.preventDefault();
                this.deferredPrompt = e;
                this.canInstall = true;
            });
            window.addEventListener('appinstalled', () => {
                this.installed = true;
                this.canInstall = false;
                this.deferredPrompt = null;
            });
        },
        async install() {
            if (!this.deferredPrompt) return;
            this.deferredPrompt.prompt();
            const choice = await this.deferredPrompt.userChoice;
            if (choice.outcome === 'accepted') {
                this.installed = true;
                this.canInstall = false;
            }
            this.deferredPrompt = null;
        }
    }">
        <p class="section-title mb-2">📱 Instal Aplikasi (PWA)</p>
        <p class="text-sm text-slate-400 mb-4">Instal SobatSaldo sebagai aplikasi di perangkatmu untuk akses lebih cepat dan fitur offline.</p>

        <div x-show="installed" class="p-3 rounded-2xl bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300 text-sm font-semibold text-center">
            ✅ SobatSaldo sudah terinstal di perangkat ini!
        </div>

        <button x-show="canInstall && !installed" @click="install()"
                class="btn-primary w-full py-3 text-sm flex items-center justify-center gap-2">
            📲 Instal sebagai Aplikasi
        </button>

        <div x-show="!canInstall && !installed" class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-700/50 text-slate-400 text-sm text-center">
            Buka di browser yang mendukung PWA (Chrome, Edge, Safari) untuk menginstal.
        </div>
    </div>

    {{-- ─ Logout ────────────────────────────────────────────────────────────── --}}
    <div class="card p-5">
        <p class="section-title mb-4">🚪 Keluar Akun</p>
        <form x-data method="POST" action="{{ route('logout') }}"
              @submit.prevent="SobatSwal.fire({
                  title: 'Keluar Akun?',
                  html: '<p class=\'text-sm\'>Yakin ingin keluar dari SobatSaldo?</p>',
                  icon: 'question',
                  showCancelButton: true,
                  confirmButtonText: '🚪 Ya, Keluar',
                  cancelButtonText: 'Batal',
                  reverseButtons: true,
                  focusCancel: true
              }).then(res => { if (res.isConfirmed) $el.submit(); })">
            @csrf
            <button type="submit"
                    class="w-full py-3 rounded-full bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 font-bold text-sm hover:bg-rose-100 dark:hover:bg-rose-900/40 transition-colors">
                🚪 Keluar dari Akun
            </button>
        </form>
    </div>

</div>

</x-layouts.app>
