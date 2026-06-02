{{--
    COMPONENT: modal.blade.php
    Props:
        $title — Modal title (string)
        $id    — Unique ID for this modal (string, no spaces)
        $size  — 'sm' | 'md' | 'lg' | 'xl' (default: 'md')
    Usage:
        <x-modal title="Tambah Kata Kunci" id="add-keyword">
            ...slot content...
        </x-modal>

    Trigger open:  @click="$dispatch('open-modal',  { id: 'add-keyword' })"
    Trigger close: @click="$dispatch('close-modal', { id: 'add-keyword' })"
    OR via store:  @click="$store.ui.modal('add-keyword')"
                  @click="$store.ui.closeModal()"
--}}

@props([
    'title' => 'Modal',
    'id'    => 'modal',
    'size'  => 'md',
])

@php
$sizeClass = match($size) {
    'sm'  => 'max-w-sm',
    'lg'  => 'max-w-2xl',
    'xl'  => 'max-w-4xl',
    default => 'max-w-lg',
};
@endphp

<div
    x-data="{ open: false }"
    @open-modal.window="if ($event.detail.id === '{{ $id }}') { open = true; document.body.style.overflow = 'hidden'; }"
    @close-modal.window="if ($event.detail.id === '{{ $id }}') { open = false; document.body.style.overflow = ''; }"
    @keydown.escape.window="if (open) { open = false; document.body.style.overflow = ''; }"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    role="dialog"
    aria-modal="true"
    aria-labelledby="modal-title-{{ $id }}"
    style="display: none;"
>
    {{-- Backdrop: klik untuk tutup --}}
    <div
        class="absolute inset-0 bg-black/40 backdrop-blur-sm"
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="open = false; document.body.style.overflow = '';"
    ></div>

    {{-- Modal Panel --}}
    <div
        class="relative w-full {{ $sizeClass }} bg-white dark:bg-slate-800 rounded-3xl shadow-2xl
               flex flex-col max-h-[90vh] overflow-hidden"
        x-show="open"
        x-transition:enter="transition ease-out duration-250"
        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-4"
        @click.stop
        @click.outside="open = false; document.body.style.overflow = '';"
    >
        {{-- Modal Header --}}
        <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100 dark:border-slate-700/50 shrink-0">
            <h2 id="modal-title-{{ $id }}" class="text-lg font-bold text-slate-900 dark:text-slate-100">
                {{ $title }}
            </h2>
            <button
                @click="open = false; document.body.style.overflow = '';"
                class="btn-icon"
                aria-label="Tutup modal"
                type="button"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Modal Content --}}
        <div class="flex-1 overflow-y-auto px-6 py-6">
            {{ $slot }}
        </div>

        {{-- Modal Footer (optional slot) --}}
        @isset($footer)
        <div class="shrink-0 px-6 py-4 border-t border-slate-100 dark:border-slate-700/50 flex items-center justify-end gap-3">
            {{ $footer }}
        </div>
        @endisset
    </div>
</div>
