{{--
    COMPONENT: bottom-sheet.blade.php
    Props:
        $title — Sheet title (string)
        $id    — Unique ID (string, no spaces)
    Usage:
        <x-bottom-sheet title="Detail Transaksi" id="detail">
            ...slot content...
        </x-bottom-sheet>

    Trigger open:  @click="$dispatch('open-sheet',  { id: 'detail' })"
    Trigger close: @click="$dispatch('close-sheet', { id: 'detail' })"
    OR via store:  @click="$store.ui.sheet('detail')"
                  @click="$store.ui.closeSheet()"
--}}

@props([
    'title' => 'Detail',
    'id'    => 'sheet',
])

<div
    x-data="{ open: false }"
    @open-sheet.window="if ($event.detail.id === '{{ $id }}') { open = true; document.body.style.overflow = 'hidden'; }"
    @close-sheet.window="if ($event.detail.id === '{{ $id }}') { open = false; document.body.style.overflow = ''; }"
    @keydown.escape.window="if (open) { open = false; document.body.style.overflow = ''; }"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50"
    role="dialog"
    aria-modal="true"
    aria-labelledby="sheet-title-{{ $id }}"
>
    {{-- Backdrop: klik untuk tutup --}}
    <div
        class="absolute inset-0 bg-black/40 backdrop-blur-sm"
        x-show="open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="open = false; document.body.style.overflow = '';"
    ></div>

    {{-- ======= MOBILE: Slides dari bawah ======= --}}
    <div
        class="absolute bottom-0 left-0 right-0 lg:hidden
               bg-white dark:bg-slate-800 rounded-t-3xl shadow-2xl
               max-h-[90vh] flex flex-col overflow-hidden"
        x-show="open"
        x-transition:enter="transition ease-out duration-350"
        x-transition:enter-start="opacity-0 translate-y-full"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-250"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-full"
        @click.stop
    >
        {{-- Drag Handle --}}
        <div class="flex justify-center pt-3 pb-1 shrink-0">
            <div class="w-12 h-1.5 bg-slate-200 dark:bg-slate-600 rounded-full"></div>
        </div>

        {{-- Sheet Header --}}
        <div class="flex items-center justify-between px-6 py-4 shrink-0">
            <h2 id="sheet-title-{{ $id }}" class="text-lg font-bold text-slate-900 dark:text-slate-100">
                {{ $title }}
            </h2>
            <button
                @click="open = false; document.body.style.overflow = '';"
                class="btn-icon w-8 h-8 text-slate-400"
                aria-label="Tutup"
                type="button"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Sheet Content --}}
        <div class="flex-1 overflow-y-auto px-6 pb-8">
            {{ $slot }}
        </div>
    </div>

    {{-- ======= DESKTOP: Panel dari kanan ======= --}}
    <div
        class="absolute top-0 right-0 bottom-0 hidden lg:flex flex-col
               w-96 bg-white dark:bg-slate-800 shadow-2xl"
        x-show="open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-x-full"
        x-transition:enter-end="opacity-100 translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-x-0"
        x-transition:leave-end="opacity-0 translate-x-full"
        @click.stop
    >
        {{-- Panel Header --}}
        <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100 dark:border-slate-700/50 shrink-0">
            <h2 id="sheet-title-{{ $id }}-desktop" class="text-lg font-bold text-slate-900 dark:text-slate-100">
                {{ $title }}
            </h2>
            <button
                @click="open = false; document.body.style.overflow = '';"
                class="btn-icon"
                aria-label="Tutup"
                type="button"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Panel Content --}}
        <div class="flex-1 overflow-y-auto px-6 py-6">
            {{ $slot }}
        </div>
    </div>
</div>
