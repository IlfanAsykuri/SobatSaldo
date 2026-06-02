<!DOCTYPE html>
<html lang="id" x-data="{ dark: localStorage.getItem('sobatsaldo_dark') === 'true' }" :class="{ 'dark': dark }">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
    <meta name="theme-color" content="#059669" />
    <meta name="description" content="{{ $metaDescription ?? 'SobatSaldo — Catat Keuanganmu Tanpa Ribet' }}" />
    <title>{{ $title ?? 'SobatSaldo' }}</title>

    {{-- Favicon --}}
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>💰</text></svg>" />

    {{-- Vite: Tailwind CSS v4 + Alpine.js --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 dark:bg-slate-900 transition-colors duration-300">

    {{-- Animated background blobs --}}
    <div class="fixed inset-0 pointer-events-none overflow-hidden" aria-hidden="true">
        <div class="absolute -top-40 -right-40 w-96 h-96 rounded-full bg-emerald-100/60 dark:bg-emerald-900/20 blur-3xl animate-float"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 rounded-full bg-emerald-200/40 dark:bg-emerald-800/20 blur-3xl animate-float" style="animation-delay: 1.5s;"></div>
    </div>

    {{-- Centered content wrapper --}}
    <div class="relative z-10 min-h-screen flex flex-col items-center justify-center px-4 py-12">

        {{-- Logo --}}
        <div class="mb-8 flex flex-col items-center gap-2 animate-fade-in">
            <div class="w-14 h-14 rounded-2xl bg-gradient-brand flex items-center justify-center shadow-lg shadow-emerald-200 dark:shadow-emerald-900/40">
                <span class="text-2xl">💰</span>
            </div>
            <span class="text-xl font-extrabold tracking-tight text-slate-900 dark:text-slate-100">
                Sobat<span class="text-emerald-600">Saldo</span>
            </span>
        </div>

        {{-- Page Content --}}
        <div class="w-full max-w-md animate-fade-in" style="animation-delay: 0.1s;">
            {{ $slot }}
        </div>

        {{-- Footer --}}
        <p class="mt-8 text-xs text-slate-400 dark:text-slate-600 text-center">
            &copy; {{ date('Y') }} SobatSaldo. Dibuat dengan ❤️ untuk finansialmu.
        </p>
    </div>

</body>
</html>
