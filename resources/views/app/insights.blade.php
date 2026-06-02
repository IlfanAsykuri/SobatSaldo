<x-layouts.app title="Evaluasi" metaDescription="Evaluasi keuangan bulananmu dengan insight cerdas.">

<div class="max-w-3xl mx-auto px-4 py-6 lg:py-8 pb-24 lg:pb-8">

    {{-- Header + Filter Bulan --}}
    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Evaluasi 📊</h1>
            <p class="text-sm text-slate-400 mt-0.5">{{ \Carbon\Carbon::createFromDate($year, $month)->translatedFormat('F Y') }}</p>
        </div>
        <form method="GET" action="{{ route('app.insights') }}" class="flex items-center gap-2">
            <select name="month" onchange="this.form.submit()" class="input-field text-sm pr-8">
                @if($availableMonths->isEmpty())
                    <option>{{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</option>
                @else
                    @foreach($availableMonths as $m)
                        <option value="{{ $m['month'] }}" data-year="{{ $m['year'] }}"
                            {{ $m['month'] == $month && $m['year'] == $year ? 'selected' : '' }}>
                            {{ $m['label'] }}
                        </option>
                    @endforeach
                @endif
            </select>
            <input type="hidden" name="year" value="{{ $year }}" id="insights-year" />
        </form>
    </div>

    {{-- Script untuk handle month + year --}}
    <script>
        document.querySelector('select[name=month]')?.addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            document.getElementById('insights-year').value = selected.dataset.year || new Date().getFullYear();
        });
    </script>

    {{-- Storyline Banner --}}
    <div class="card p-5 mb-6 flex items-start gap-4 {{ $storyline['positive'] ? 'bg-emerald-50 dark:bg-emerald-900/20' : 'bg-amber-50 dark:bg-amber-900/20' }}">
        <span class="text-3xl shrink-0">{{ $storyline['emoji'] }}</span>
        <p class="text-sm font-semibold {{ $storyline['positive'] ? 'text-emerald-700 dark:text-emerald-300' : 'text-amber-700 dark:text-amber-300' }} leading-relaxed">
            {{ $storyline['text'] }}
        </p>
    </div>

    {{-- Metric Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="card p-4">
            <p class="text-xs text-slate-400 font-semibold uppercase tracking-wide mb-1">Pemasukan</p>
            <p class="text-lg font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
        </div>
        <div class="card p-4">
            <p class="text-xs text-slate-400 font-semibold uppercase tracking-wide mb-1">Pengeluaran</p>
            <p class="text-lg font-bold text-rose-500">Rp {{ number_format($totalExpense, 0, ',', '.') }}</p>
        </div>
        <div class="card p-4">
            <p class="text-xs text-slate-400 font-semibold uppercase tracking-wide mb-1">Batas Aman/Hari</p>
            <p class="text-lg font-bold {{ $dailySafe > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-500' }}">
                Rp {{ number_format($dailySafe, 0, ',', '.') }}
            </p>
        </div>
        <div class="card p-4">
            <p class="text-xs text-slate-400 font-semibold uppercase tracking-wide mb-1">Rata-rata/Hari</p>
            <p class="text-lg font-bold text-slate-700 dark:text-slate-300">Rp {{ number_format($avgPerDay, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Donut Chart + Top Kategori --}}
    @if($topCategories->isNotEmpty())
    <div class="card p-5 mb-6">
        <p class="section-title mb-4">🍩 Top Kategori Pengeluaran</p>
        <div class="flex items-center gap-6 flex-wrap">

            {{-- SVG Donut --}}
            @php
            $colors_hex  = ['#f43f5e', '#f59e0b', '#3b82f6'];
            $strokeDash  = 2 * 3.14159 * 40; // circumference r=40
            $offset      = 0;
            $segments    = [];
            $runningPct  = 0;
            foreach ($topCategories as $i => $cat) {
                $pct = $cat->percentage ?? 0;
                $dash = $strokeDash * ($pct / 100);
                $segments[] = [
                    'color'  => $colors_hex[$i] ?? '#94a3b8',
                    'dash'   => $dash,
                    'gap'    => $strokeDash - $dash,
                    'offset' => $strokeDash - ($strokeDash * $runningPct / 100),
                    'pct'    => $pct,
                    'name'   => $cat->category->name ?? 'Lain-lain',
                ];
                $runningPct += $pct;
            }
            @endphp

            <div class="relative shrink-0">
                <svg width="120" height="120" viewBox="0 0 120 120" class="-rotate-90">
                    <circle cx="60" cy="60" r="40" fill="none" stroke="#e2e8f0" stroke-width="18" class="dark:stroke-slate-700"/>
                    @foreach($segments as $seg)
                    <circle cx="60" cy="60" r="40" fill="none"
                            stroke="{{ $seg['color'] }}"
                            stroke-width="18"
                            stroke-dasharray="{{ $seg['dash'] }} {{ $seg['gap'] }}"
                            stroke-dashoffset="{{ $seg['offset'] }}"
                            stroke-linecap="round"
                    />
                    @endforeach
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="text-center">
                        <p class="text-sm font-bold text-slate-800 dark:text-white">{{ $totalTransactions }}</p>
                        <p class="text-xs text-slate-400">transaksi</p>
                    </div>
                </div>
            </div>

            <div class="flex-1 space-y-3">
                @foreach($topCategories as $i => $cat)
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full shrink-0" style="background: {{ $colors_hex[$i] ?? '#94a3b8' }}"></span>
                            <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ $cat->category->name ?? 'Lain-lain' }}</span>
                        </div>
                        <span class="text-xs font-bold text-slate-500">{{ $cat->percentage }}%</span>
                    </div>
                    <div class="h-2 rounded-full bg-slate-100 dark:bg-slate-700">
                        <div class="h-2 rounded-full transition-all duration-700" style="width: {{ $cat->percentage }}%; background: {{ $colors_hex[$i] ?? '#94a3b8' }}"></div>
                    </div>
                    <p class="text-xs text-slate-400 mt-0.5">Rp {{ number_format($cat->total, 0, ',', '.') }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @else
    <div class="card p-8 mb-6 text-center">
        <p class="text-3xl mb-2">📊</p>
        <p class="text-slate-500 dark:text-slate-400">Belum ada data pengeluaran untuk bulan ini.</p>
    </div>
    @endif

    {{-- 7-Day Bar Chart --}}
    <div class="card p-5 mb-6">
        <div class="flex items-center justify-between mb-4">
            <p class="section-title">📅 Pengeluaran 7 Hari Terakhir</p>
            <p class="text-xs text-slate-400">Batas aman: Rp {{ number_format($dailySafe, 0, ',', '.') }}/hari</p>
        </div>
        @php $maxDaily = $last7Days->max('total') ?: 1; @endphp
        <div class="flex items-end gap-1.5 h-28">
            @foreach($last7Days as $day)
            @php $barHeight = max(($day['total'] / $maxDaily) * 100, $day['total'] > 0 ? 5 : 0); @endphp
            <div class="flex-1 flex flex-col items-center gap-1">
                <p class="text-[9px] text-slate-400 truncate w-full text-center">
                    {{ $day['total'] > 0 ? 'Rp ' . number_format($day['total']/1000, 0) . 'rb' : '' }}
                </p>
                <div class="w-full rounded-t-xl transition-all duration-700 {{ $day['is_over'] ? 'bg-rose-400' : 'bg-emerald-400 dark:bg-emerald-500' }}"
                     style="height: {{ $barHeight }}%;"></div>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-semibold">{{ $day['label'] }}</p>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Achievement Stats --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="card p-4 text-center">
            <p class="text-2xl font-bold text-amber-500">🔥{{ $streak }}</p>
            <p class="text-xs text-slate-400 mt-1 font-semibold">Hari Streak</p>
        </div>
        <div class="card p-4 text-center">
            <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $totalTransactions }}</p>
            <p class="text-xs text-slate-400 mt-1 font-semibold">Transaksi</p>
        </div>
        <div class="card p-4 text-center">
            <p class="text-2xl font-bold text-blue-500">{{ $walletCount }}</p>
            <p class="text-xs text-slate-400 mt-1 font-semibold">Dompet Aktif</p>
        </div>
    </div>

</div>

</x-layouts.app>
