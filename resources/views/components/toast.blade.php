{{--
    COMPONENT: toast.blade.php
    Usage: <x-toast />  — place once in layout.
    Trigger via Alpine global: $dispatch('toast', { type: 'success', message: 'Berhasil disimpan!' })
    Types: 'success' | 'error' | 'warning' | 'info'
--}}
<div
    x-data="{
        show: false,
        type: 'success',
        message: '',
        icon: '✅',
        timer: null,
        open(event) {
            clearTimeout(this.timer);
            this.type    = event.detail.type    ?? 'success';
            this.message = event.detail.message ?? 'Berhasil!';
            this.icon = {
                success: '✅',
                error:   '❌',
                warning: '⚠️',
                info:    'ℹ️',
            }[this.type] ?? '✅';
            this.show = true;
            if (navigator.vibrate) {
                this.type === 'error'
                    ? navigator.vibrate([50, 30, 50])
                    : navigator.vibrate(50);
            }
            this.timer = setTimeout(() => this.show = false, 3000);
        }
    }"
    @toast.window="open($event)"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 -translate-y-4 scale-95"
    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
    x-transition:leave-end="opacity-0 -translate-y-4 scale-95"
    class="fixed z-50 pointer-events-none
           top-4 left-1/2 -translate-x-1/2
           lg:top-auto lg:bottom-6 lg:right-6 lg:left-auto lg:translate-x-0"
    style="display: none;"
    aria-live="polite"
    role="alert"
>
    <div
        class="flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-xl backdrop-blur-sm pointer-events-auto
               min-w-[280px] max-w-[90vw] lg:max-w-sm"
        :class="{
            'bg-emerald-50/95 dark:bg-emerald-900/95 text-emerald-800 dark:text-emerald-200 shadow-emerald-200/50 dark:shadow-emerald-900/50': type === 'success',
            'bg-rose-50/95   dark:bg-rose-900/95   text-rose-800   dark:text-rose-200   shadow-rose-200/50   dark:shadow-rose-900/50':   type === 'error',
            'bg-amber-50/95  dark:bg-amber-900/95  text-amber-800  dark:text-amber-200  shadow-amber-200/50  dark:shadow-amber-900/50':  type === 'warning',
            'bg-blue-50/95   dark:bg-blue-900/95   text-blue-800   dark:text-blue-200   shadow-blue-200/50   dark:shadow-blue-900/50':   type === 'info',
        }"
    >
        {{-- Icon --}}
        <span class="text-lg shrink-0" x-text="icon"></span>

        {{-- Message --}}
        <p class="flex-1 text-sm font-semibold leading-snug" x-text="message"></p>

        {{-- Progress bar --}}
        <div class="absolute bottom-0 left-0 right-0 h-0.5 rounded-b-2xl overflow-hidden">
            <div
                class="h-full rounded-full"
                :class="{
                    'bg-emerald-500': type === 'success',
                    'bg-rose-500':   type === 'error',
                    'bg-amber-500':  type === 'warning',
                    'bg-blue-500':   type === 'info',
                }"
                x-show="show"
                style="animation: shrink-bar 3s linear forwards;"
            ></div>
        </div>
    </div>
</div>

<style>
    @keyframes shrink-bar {
        from { width: 100%; }
        to   { width: 0%; }
    }
</style>
