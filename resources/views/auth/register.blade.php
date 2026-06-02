<x-layouts.guest title="Daftar" metaDescription="Buat akun SobatSaldo gratis dan mulai catat keuanganmu hari ini.">

    <div class="card p-8 space-y-6">

        {{-- Heading --}}
        <div class="space-y-1">
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-slate-100">Buat Akun Baru ✨</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Gratis selamanya. Tidak perlu kartu kredit.</p>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route('register') }}" class="space-y-4"
              x-data="{ showPass: false, showConfirm: false, loading: false, strength: 0,
                         checkStrength(p) {
                             let s = 0;
                             if(p.length >= 8) s += 25;
                             if(/[A-Z]/.test(p)) s += 25;
                             if(/[0-9]/.test(p)) s += 25;
                             if(/[^A-Za-z0-9]/.test(p)) s += 25;
                             this.strength = s;
                         }
                       }"
              @submit="setTimeout(() => loading = true, 50)">
            @csrf

            @if ($errors->any())
                <div class="p-3 rounded-xl bg-rose-50 text-rose-600 text-sm font-medium">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Nama --}}
            <div class="space-y-1.5">
                <label for="name" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">
                    Nama Panggilan
                </label>
                <input id="name" type="text" name="name" placeholder="Ilfan Asykuri" autocomplete="name" required class="input-field" value="{{ old('name') }}" />
            </div>

            {{-- Email --}}
            <div class="space-y-1.5">
                <label for="email" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">
                    Email
                </label>
                <input id="email" type="email" name="email" placeholder="kamu@email.com" autocomplete="email" required class="input-field" value="{{ old('email') }}" />
            </div>

            {{-- Password --}}
            <div class="space-y-1.5">
                <label for="password" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">
                    Password
                </label>
                <div class="relative">
                    <input id="password" :type="showPass ? 'text' : 'password'" name="password" placeholder="Minimal 8 karakter" required @input="checkStrength($event.target.value)" class="input-field pr-11" />
                    <button type="button" @click="showPass = !showPass" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                        <svg x-show="!showPass" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg x-show="showPass" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                    </button>
                </div>
                {{-- Strength Meter --}}
                <div class="flex gap-1 mt-1.5 h-1">
                    <div class="flex-1 rounded-full bg-slate-200 dark:bg-slate-700 overflow-hidden">
                        <div class="h-full bg-rose-500 transition-all duration-300" :style="`width: ${strength >= 25 ? '100%' : '0%'}`"></div>
                    </div>
                    <div class="flex-1 rounded-full bg-slate-200 dark:bg-slate-700 overflow-hidden">
                        <div class="h-full bg-amber-500 transition-all duration-300" :style="`width: ${strength >= 50 ? '100%' : '0%'}`"></div>
                    </div>
                    <div class="flex-1 rounded-full bg-slate-200 dark:bg-slate-700 overflow-hidden">
                        <div class="h-full bg-emerald-500 transition-all duration-300" :style="`width: ${strength >= 75 ? '100%' : '0%'}`"></div>
                    </div>
                    <div class="flex-1 rounded-full bg-slate-200 dark:bg-slate-700 overflow-hidden">
                        <div class="h-full bg-emerald-600 transition-all duration-300" :style="`width: ${strength == 100 ? '100%' : '0%'}`"></div>
                    </div>
                </div>
                <p class="text-[10px] text-slate-400 mt-1" x-show="strength > 0">
                    <span x-show="strength <= 25" class="text-rose-500 font-semibold">Lemah</span>
                    <span x-show="strength == 50" class="text-amber-500 font-semibold">Cukup</span>
                    <span x-show="strength == 75" class="text-emerald-500 font-semibold">Kuat</span>
                    <span x-show="strength == 100" class="text-emerald-600 font-semibold">Sangat Kuat</span>
                </p>
            </div>

            {{-- Confirm Password --}}
            <div class="space-y-1.5">
                <label for="password_confirmation" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">
                    Konfirmasi Password
                </label>
                <div class="relative">
                    <input id="password_confirmation" :type="showConfirm ? 'text' : 'password'" name="password_confirmation" placeholder="Ulangi password" required class="input-field pr-11" />
                    <button type="button" @click="showConfirm = !showConfirm" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                        <svg x-show="!showConfirm" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg x-show="showConfirm" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                    </button>
                </div>
            </div>

            {{-- Submit --}}
            <button type="submit" class="btn-primary w-full py-3.5 text-base mt-2 shadow-lg shadow-emerald-200/60 dark:shadow-emerald-900/40" :class="{ 'pointer-events-none opacity-75': loading }">
                <svg x-show="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                <span x-text="loading ? 'Memproses...' : 'Daftar Sekarang'">Daftar Sekarang</span>
            </button>

            {{-- Terms note --}}
            <p class="text-xs text-slate-400 dark:text-slate-500 text-center leading-relaxed">
                Dengan mendaftar, kamu menyetujui
                <a href="#" class="underline hover:text-emerald-600">Syarat Layanan</a> dan
                <a href="#" class="underline hover:text-emerald-600">Kebijakan Privasi</a> kami.
            </p>
        </form>

        {{-- Login Link --}}
        <p class="text-center text-sm text-slate-500 dark:text-slate-400">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="text-emerald-600 dark:text-emerald-400 font-semibold hover:underline">
                Masuk di sini
            </a>
        </p>
    </div>

</x-layouts.guest>
