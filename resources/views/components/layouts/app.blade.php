<!DOCTYPE html>
<html lang="id"
    x-data="{
        dark: localStorage.getItem('sobatsaldo_dark') === 'true',
        sidebarOpen: window.innerWidth >= 1024,
        currentPage: '{{ request()->segment(2) ?? 'workspace' }}'
    }"
    :class="{ 'dark': dark }"
    x-init="$watch('dark', val => localStorage.setItem('sobatsaldo_dark', val))"
>
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
    <meta name="theme-color" content="#059669" />
    <meta name="description" content="{{ $metaDescription ?? 'SobatSaldo — Lacak Keuanganmu Secara Cerdas' }}" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ $title ?? 'SobatSaldo' }} — SobatSaldo</title>

    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>💰</text></svg>" />
    <link rel="manifest" href="/manifest.json" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 transition-colors duration-300 overflow-x-hidden">

    {{-- ===================================================
         DESKTOP LAYOUT (lg+) — Left Sidebar
    ==================================================== --}}
    <div class="hidden lg:flex min-h-screen">

        {{-- SIDEBAR --}}
        <aside class="fixed left-0 top-0 bottom-0 w-64 bg-white dark:bg-slate-800 shadow-[var(--shadow-sidebar)] flex flex-col z-30">

            {{-- Logo --}}
            <div class="px-6 py-6 border-b border-slate-100 dark:border-slate-700/50">
                <a href="{{ route('app.workspace') }}" class="flex items-center gap-3 group">
                    <div class="w-9 h-9 rounded-xl bg-gradient-brand flex items-center justify-center shadow-md shadow-emerald-200 dark:shadow-emerald-900/30 group-hover:scale-105 transition-transform">
                        <span class="text-lg">💰</span>
                    </div>
                    <span class="text-lg font-extrabold tracking-tight">
                        Sobat<span class="text-emerald-600">Saldo</span>
                    </span>
                </a>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                <p class="section-title px-4 mb-4">Menu Utama</p>

                <a href="{{ route('app.workspace') }}"
                   class="nav-item {{ request()->routeIs('app.workspace') ? 'active' : '' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                    </svg>
                    <span>Catat</span>
                </a>

                <a href="{{ route('app.wallet') }}"
                   class="nav-item {{ request()->routeIs('app.wallet') ? 'active' : '' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                    <span>Dompet</span>
                </a>

                <a href="{{ route('app.insights') }}"
                   class="nav-item {{ request()->routeIs('app.insights') ? 'active' : '' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <span>Insights</span>
                </a>

                <a href="{{ route('app.dictionary') }}"
                   class="nav-item {{ request()->routeIs('app.dictionary') ? 'active' : '' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <span>Kamus</span>
                </a>

                <a href="{{ route('app.settings') }}"
                   class="nav-item {{ request()->routeIs('app.settings') ? 'active' : '' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>Pengaturan</span>
                </a>
            </nav>

            {{-- User Avatar --}}
            <div class="px-4 py-4 border-t border-slate-100 dark:border-slate-700/50">
                <a href="{{ route('app.settings') }}" class="flex items-center gap-3 px-2 rounded-2xl hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors py-1.5 group">
                    <div class="w-9 h-9 rounded-full bg-gradient-brand flex items-center justify-center text-white font-bold text-sm shrink-0">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-200 truncate group-hover:text-emerald-600 transition-colors">{{ auth()->user()->name ?? 'Pengguna' }}</p>
                        <p class="text-xs text-slate-400 truncate">{{ auth()->user()->email ?? '' }}</p>
                    </div>
                </a>
            </div>
        </aside>

        {{-- MAIN CONTENT (Desktop) --}}
        <main class="flex-1 ml-64 min-h-screen">
            {{-- Global Toast --}}
            <x-toast />

            {{-- Page Content --}}
            <div class="animate-fade-in">
                {{ $slot }}
            </div>
        </main>
    </div>


    {{-- ===================================================
         MOBILE LAYOUT (< lg) — Bottom Navigation Bar
    ==================================================== --}}
    <div class="lg:hidden flex flex-col min-h-screen">

        {{-- MOBILE MAIN CONTENT --}}
        <main class="flex-1 pb-20 overflow-y-auto">
            {{-- Global Toast --}}
            <x-toast />

            {{-- Page Content --}}
            <div class="animate-fade-in">
                {{ $slot }}
            </div>
        </main>

        {{-- BOTTOM NAVIGATION BAR --}}
        <nav class="fixed bottom-0 left-0 right-0 z-30 bg-white/95 dark:bg-slate-800/95 backdrop-blur-md shadow-[var(--shadow-bottom-nav)]">
            <div class="flex items-center justify-around px-2 py-2 max-w-lg mx-auto">

                {{-- Kamus --}}
                <a href="{{ route('app.dictionary') }}"
                   id="nav-dictionary"
                   class="flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-2xl transition-all duration-200 min-w-0
                          {{ request()->routeIs('app.dictionary') ? 'text-emerald-600' : 'text-slate-400 hover:text-slate-600' }}">
                    <svg class="w-6 h-6" fill="{{ request()->routeIs('app.dictionary') ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ request()->routeIs('app.dictionary') ? '0' : '2' }}">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <span class="text-[10px] font-semibold">Kamus</span>
                </a>

                {{-- Dompet --}}
                <a href="{{ route('app.wallet') }}"
                   id="nav-wallet"
                   class="flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-2xl transition-all duration-200 min-w-0
                          {{ request()->routeIs('app.wallet') ? 'text-emerald-600' : 'text-slate-400 hover:text-slate-600' }}">
                    <svg class="w-6 h-6" fill="{{ request()->routeIs('app.wallet') ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ request()->routeIs('app.wallet') ? '0' : '2' }}">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                    <span class="text-[10px] font-semibold">Dompet</span>
                </a>

                {{-- CENTER FAB — Quick Add --}}
                <div class="relative -mt-6">
                    <button id="fab-add"
                            onclick="if(navigator.vibrate) navigator.vibrate(50); const input = document.getElementById('ghost-input-mobile'); if(input) { input.scrollIntoView({behavior: 'smooth', block: 'center'}); setTimeout(() => input.focus(), 300); } else { window.location.href = '{{ route('app.workspace') }}?focus=1'; }"
                            class="w-14 h-14 rounded-full bg-gradient-brand text-white shadow-lg shadow-emerald-300 dark:shadow-emerald-900/50 flex items-center justify-center hover:scale-105 active:scale-95 transition-transform duration-150 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                    </button>
                </div>

                {{-- Insights --}}
                <a href="{{ route('app.insights') }}"
                   id="nav-insights"
                   class="flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-2xl transition-all duration-200 min-w-0
                          {{ request()->routeIs('app.insights') ? 'text-emerald-600' : 'text-slate-400 hover:text-slate-600' }}">
                    <svg class="w-6 h-6" fill="{{ request()->routeIs('app.insights') ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ request()->routeIs('app.insights') ? '0' : '2' }}">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <span class="text-[10px] font-semibold">Insights</span>
                </a>

                {{-- Profil --}}
                <a href="{{ route('app.settings') }}"
                   id="nav-settings"
                   class="flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-2xl transition-all duration-200 min-w-0
                          {{ request()->routeIs('app.settings') ? 'text-emerald-600' : 'text-slate-400 hover:text-slate-600' }}">
                    <svg class="w-6 h-6" fill="{{ request()->routeIs('app.settings') ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ request()->routeIs('app.settings') ? '0' : '2' }}">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span class="text-[10px] font-semibold">Profil</span>
                </a>

            </div>
        </nav>
    </div>

</body>
</html>
