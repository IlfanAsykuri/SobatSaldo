{{--
    COMPONENT: ghost-input.blade.php
    A giant transparent autofocusing input field for the main workspace.
    Props:
        $placeholder  — Placeholder text
        $name         — Input name attribute
        $id           — Input id attribute
        $autofocus    — Boolean, auto-focus on mount
--}}

@props([
    'placeholder' => 'Ketik pengeluaran... (mis: kopi 15rb)',
    'name'        => 'input_text',
    'id'          => 'ghost-input-main',
    'autofocus'   => true,
])

<div
    x-data="{
        value: '',
        focused: false,
        charCount: 0,
        handleInput(e) {
            this.value = e.target.value;
            this.charCount = e.target.value.length;
        },
        handleSubmit() {
            if (this.value.trim() === '') return;
            if (navigator.vibrate) navigator.vibrate(50);
            this.$dispatch('toast', { type: 'success', message: 'Transaksi berhasil dicatat! ✅' });
            this.value = '';
            this.charCount = 0;
            this.$refs.input.focus();
        }
    }"
    class="w-full flex flex-col items-center gap-3"
>
    {{-- Main Ghost Input --}}
    <div class="relative w-full">
        <input
            x-ref="input"
            x-model="value"
            @input="handleInput($event)"
            @focus="focused = true"
            @blur="focused = false"
            @keydown.enter.prevent="handleSubmit()"
            type="text"
            id="{{ $id }}"
            name="{{ $name }}"
            placeholder="{{ $placeholder }}"
            {{ $autofocus ? 'autofocus' : '' }}
            autocomplete="off"
            autocorrect="off"
            spellcheck="false"
            class="ghost-input w-full text-center
                   transition-all duration-300
                   dark:placeholder-slate-600"
            :class="{
                'text-3xl lg:text-5xl': value.length > 20,
                'text-4xl lg:text-6xl': value.length <= 20 && value.length > 0,
                'text-5xl lg:text-7xl': value.length === 0
            }"
            aria-label="Input transaksi utama"
        />

        {{-- Underline accent (visible when focused or has value) --}}
        <div
            class="absolute bottom-0 left-1/2 -translate-x-1/2 h-0.5 bg-gradient-brand rounded-full transition-all duration-300"
            :class="focused || value ? 'w-24 opacity-100' : 'w-0 opacity-0'"
        ></div>
    </div>

    {{-- Character hint / helper --}}
    <div
        class="flex items-center gap-2 transition-all duration-300"
        :class="focused || value ? 'opacity-100' : 'opacity-0'"
    >
        <span class="text-xs text-slate-400 dark:text-slate-500">
            <template x-if="value.length === 0">
                <span>Tekan <kbd class="px-1.5 py-0.5 bg-slate-100 dark:bg-slate-700 rounded-lg text-slate-500 font-mono text-xs">Enter</kbd> untuk menyimpan</span>
            </template>
            <template x-if="value.length > 0">
                <span x-text="`${charCount} karakter — tekan Enter untuk simpan`"></span>
            </template>
        </span>
    </div>

    {{-- Submit Button (visible on mobile tap) --}}
    <button
        @click="handleSubmit()"
        x-show="value.length > 0"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-90"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-90"
        class="btn-primary px-8 py-3 text-base mt-1 shadow-lg shadow-emerald-200/60 dark:shadow-emerald-900/40"
        type="button"
    >
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
        </svg>
        Catat Sekarang
    </button>
</div>
