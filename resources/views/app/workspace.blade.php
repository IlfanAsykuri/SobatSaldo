<x-layouts.app title="Catat" metaDescription="Catat transaksi keuanganmu secara cepat dan mudah.">

@php
    function getHabitIcon($keyword) {
        $k = strtolower($keyword);
        if (str_contains($k, 'makan') || str_contains($k, 'food') || str_contains($k, 'sarapan') || str_contains($k, 'gorengan')) return '🍽️';
        if (str_contains($k, 'minum') || str_contains($k, 'kopi') || str_contains($k, 'es') || str_contains($k, 'boba') || str_contains($k, 'air')) return '🥤';
        if (str_contains($k, 'bensin') || str_contains($k, 'bbm') || str_contains($k, 'pertamax') || str_contains($k, 'pertalite')) return '⛽';
        if (str_contains($k, 'laundry') || str_contains($k, 'cuci') || str_contains($k, 'baju')) return '👕';
        if (str_contains($k, 'parkir') || str_contains($k, 'tol')) return '🅿️';
        if (str_contains($k, 'pulsa') || str_contains($k, 'paket') || str_contains($k, 'data') || str_contains($k, 'internet')) return '📱';
        if (str_contains($k, 'listrik') || str_contains($k, 'token') || str_contains($k, 'pln')) return '💡';
        if (str_contains($k, 'belanja') || str_contains($k, 'mart') || str_contains($k, 'rokok')) return '🛒';
        if (str_contains($k, 'gaji') || str_contains($k, 'bonus') || str_contains($k, 'thr')) return '💰';
        return '📌';
    }
@endphp

{{-- ── FLASH MESSAGES ─────────────────────────────────────────────────────── --}}
@if (session('success'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
     x-transition class="fixed top-4 right-4 z-50 p-4 rounded-2xl bg-emerald-500 text-white shadow-xl max-w-sm text-sm font-semibold flex items-center gap-2">
    <span>✅</span> {{ session('success') }}
</div>
@endif
@if (session('error'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
     x-transition class="fixed top-4 right-4 z-50 p-4 rounded-2xl bg-rose-500 text-white shadow-xl max-w-sm text-sm font-semibold flex items-center gap-2">
    <span>⚠️</span> {{ session('error') }}
</div>
@endif

{{-- ═══════════════════════════════════════════════════════════════════════════
     DESKTOP: 2-Column Layout
═══════════════════════════════════════════════════════════════════════════ --}}
<div class="hidden lg:grid lg:grid-cols-[1fr_320px] min-h-screen">

    {{-- ═ DESKTOP: Kolom Kiri (Tabel + Filter) ═ --}}
    <div class="flex flex-col p-6 gap-6 overflow-y-auto">

        {{-- Header: Sapaan + Streak --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-white">
                    Halo, {{ Str::words(Auth::user()->name, 1, '') }}! 👋
                </h1>
                <p class="text-sm text-slate-400 mt-0.5">
                    @if($streak > 0)
                        <span class="text-amber-500 font-bold">🔥 Streak {{ $streak }} hari berturut-turut!</span>
                    @else
                        Mulai catat hari ini untuk memulai streak kamu!
                    @endif
                </p>
            </div>
            {{-- Safe-to-Spend --}}
            <div class="text-right">
                <p class="text-xs text-slate-400 uppercase tracking-wide font-semibold">Batas Aman Hari Ini</p>
                <p class="text-xl font-bold {{ $safeToSpend > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-500' }}">
                    Rp {{ number_format($safeToSpend, 0, ',', '.') }}
                </p>
            </div>
        </div>

        {{-- Metric Cards --}}
        <div class="grid grid-cols-3 gap-4">
            <div class="card p-4">
                <p class="text-xs text-slate-400 uppercase tracking-wide font-semibold mb-1">Pemasukan</p>
                <p class="text-lg font-bold text-emerald-600 dark:text-emerald-400">
                    Rp {{ number_format($totalIncome, 0, ',', '.') }}
                </p>
            </div>
            <div class="card p-4">
                <p class="text-xs text-slate-400 uppercase tracking-wide font-semibold mb-1">Pengeluaran</p>
                <p class="text-lg font-bold text-rose-500">
                    Rp {{ number_format($totalExpense, 0, ',', '.') }}
                </p>
            </div>
            <div class="card p-4">
                <p class="text-xs text-slate-400 uppercase tracking-wide font-semibold mb-1">Bersih</p>
                @php $net = $totalIncome - $totalExpense; @endphp
                <p class="text-lg font-bold {{ $net >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-500' }}">
                    Rp {{ number_format(abs($net), 0, ',', '.') }}
                </p>
            </div>
        </div>

        {{-- Filter Bar --}}
        <form method="GET" action="{{ route('app.workspace') }}" class="card p-4 flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[150px]">
                <label class="section-title block mb-1">Cari Transaksi</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari deskripsi..."
                       class="input-field" />
            </div>
            <div>
                <label class="section-title block mb-1">Jenis</label>
                <select name="type" class="input-field pr-8">
                    <option value="">Semua</option>
                    <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>Pengeluaran</option>
                    <option value="income"  {{ request('type') === 'income'  ? 'selected' : '' }}>Pemasukan</option>
                </select>
            </div>
            <div>
                <label class="section-title block mb-1">Bulan</label>
                <input type="month" name="period" value="{{ $year }}-{{ str_pad($month, 2, '0', STR_PAD_LEFT) }}"
                       class="input-field"
                       onchange="const [y,m] = this.value.split('-'); this.form.querySelector('[name=year]').value = y; this.form.querySelector('[name=month]').value = m; this.form.submit();" />
                <input type="hidden" name="month" value="{{ $month }}" />
                <input type="hidden" name="year"  value="{{ $year }}" />
            </div>
            <button type="submit" class="btn-primary px-4 py-2.5 text-sm">Filter</button>
            @if(request()->hasAny(['search','type','month','year']))
                <a href="{{ route('app.workspace') }}" class="px-4 py-2.5 text-sm text-slate-500 hover:text-rose-500 transition-colors font-medium rounded-full">Reset</a>
            @endif
        </form>

        {{-- Transaction Table --}}
        <div class="card overflow-hidden">
            <div class="px-6 py-4 flex items-center justify-between">
                <p class="section-title">Riwayat Transaksi</p>
                <span class="text-xs text-slate-400">{{ $transactions->total() }} transaksi</span>
            </div>

            @if($transactions->isEmpty())
                <div class="px-6 py-12 text-center">
                    <p class="text-4xl mb-3">📝</p>
                    <p class="text-slate-500 dark:text-slate-400 font-medium">Belum ada transaksi untuk periode ini.</p>
                    <p class="text-slate-400 text-sm mt-1">Gunakan Ghost Input di kanan untuk mulai mencatat!</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-slate-50/80 dark:bg-slate-800/50">
                                <th class="text-left px-6 py-4 text-xs font-bold uppercase tracking-widest text-slate-400">Catatan</th>
                                <th class="text-left px-4 py-4 text-xs font-bold uppercase tracking-widest text-slate-400">Kategori</th>
                                <th class="text-left px-4 py-4 text-xs font-bold uppercase tracking-widest text-slate-400">Dompet</th>
                                <th class="text-right px-6 py-4 text-xs font-bold uppercase tracking-widest text-slate-400">Nominal</th>
                                <th class="text-center px-4 py-4 text-xs font-bold uppercase tracking-widest text-slate-400">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 dark:divide-slate-700/30">
                            @foreach ($transactions as $trx)
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-700/20 transition-colors group"
                                x-data="{
                                    editOpen: false,
                                    editRaw: '{{ addslashes($trx->raw_text) }}',
                                    editAmount: {{ (float) $trx->amount }},
                                    editType: '{{ $trx->type }}',
                                    editCategory: {{ $trx->category_id ?? 'null' }},
                                    editWallet: {{ $trx->wallet_id ?? 'null' }},
                                    editToWallet: {{ $trx->to_wallet_id ?? 'null' }},
                                }">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-2xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-lg shrink-0">
                                            {{ $trx->type === 'transfer' ? '🔄' : ($trx->type === 'income' ? '💰' : '💸') }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-slate-800 dark:text-slate-200 capitalize">{{ $trx->raw_text }}</p>
                                            <p class="text-xs text-slate-400 mt-0.5">{{ $trx->created_at->format('H:i') }} WIB</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="badge {{ $trx->type === 'transfer' ? 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300' : ($trx->type === 'income' ? 'badge-success' : 'badge-info') }}">
                                        {{ $trx->type === 'transfer' ? 'Mutasi' : ($trx->category->name ?? 'Lain-lain') }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="text-sm text-slate-500 dark:text-slate-400">
                                        {{ $trx->type === 'transfer' ? ($trx->wallet->name ?? '-') . ' ➔ ' . ($trx->toWallet->name ?? '-') : ($trx->wallet->name ?? '-') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-sm font-bold {{ $trx->type === 'transfer' ? 'text-slate-500 dark:text-slate-400' : ($trx->type === 'income' ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-800 dark:text-slate-200') }}">
                                        {{ $trx->type === 'transfer' ? '' : ($trx->type === 'income' ? '+' : '-') }}Rp {{ number_format($trx->amount, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center justify-center gap-1">
                                        {{-- Tombol Edit --}}
                                        <button @click="editOpen = true"
                                                class="p-1.5 rounded-xl text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 transition-all opacity-0 group-hover:opacity-100"
                                                title="Edit transaksi">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>

                                        {{-- Tombol Hapus --}}
                                        <button
                                            @click="confirmDelete('Transaksi &ldquo;{{ addslashes($trx->raw_text) }}&rdquo; akan dihapus permanen.').then(ok => {
                                                if (!ok) return;
                                                fetch('{{ route('transactions.destroy', $trx->id) }}', {
                                                    method: 'DELETE',
                                                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }
                                                }).then(r => r.json()).then(d => {
                                                    if (d.success) { SobatSwal.fire({ icon: 'success', title: 'Terhapus!', text: d.message, timer: 1800, timerProgressBar: true, showConfirmButton: false }).then(() => window.location.reload()); }
                                                    else alertError(d.message);
                                                });
                                            })"
                                            class="p-1.5 rounded-xl text-slate-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/30 transition-all opacity-0 group-hover:opacity-100"
                                            title="Hapus transaksi">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>

                                    {{-- Edit Overlay per-row --}}
                                    <div x-show="editOpen" x-cloak
                                         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm"
                                         @click.self="editOpen = false">
                                        <div class="card w-full max-w-md p-6 shadow-2xl" @click.outside="editOpen = false">
                                            <div class="flex items-center justify-between mb-4">
                                                <p class="text-base font-bold text-slate-800 dark:text-white">Edit Transaksi</p>
                                                <button @click="editOpen = false" class="p-1 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition-all">
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                                </button>
                                            </div>
                                            <form method="POST" action="{{ route('transactions.update', $trx->id) }}" class="space-y-3">
                                                @csrf
                                                @method('PUT')
                                                <div>
                                                    <label class="section-title block mb-1">Deskripsi</label>
                                                    <input type="text" name="raw_text" x-model="editRaw" class="input-field" required />
                                                </div>
                                                <div class="grid grid-cols-2 gap-3">
                                                    <div>
                                                        <label class="section-title block mb-1">Nominal (Rp)</label>
                                                        <input type="number" name="amount" x-model="editAmount" class="input-field" required min="0" step="100" />
                                                    </div>
                                                    <div>
                                                        <label class="section-title block mb-1">Jenis</label>
                                                        <select name="type" x-model="editType" class="input-field">
                                                            <option value="expense">Pengeluaran</option>
                                                            <option value="income">Pemasukan</option>
                                                            <option value="transfer">Mutasi / Transfer</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="grid grid-cols-2 gap-3">
                                                    <div x-show="editType !== 'transfer'">
                                                        <label class="section-title block mb-1">Kategori</label>
                                                        <select name="category_id" x-model="editCategory" class="input-field" x-bind:required="editType !== 'transfer'">
                                                            @foreach($categories as $cat)
                                                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label class="section-title block mb-1" x-text="editType === 'transfer' ? 'Dari Dompet (Sumber)' : 'Dompet'"></label>
                                                        <select name="wallet_id" x-model="editWallet" class="input-field">
                                                            <option value="">-</option>
                                                            @foreach($wallets as $w)
                                                                <option value="{{ $w->id }}">{{ $w->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div x-show="editType === 'transfer'" x-cloak>
                                                        <label class="section-title block mb-1">Ke Dompet (Tujuan)</label>
                                                        <select name="to_wallet_id" x-model="editToWallet" class="input-field">
                                                            <option value="">-</option>
                                                            @foreach($wallets as $w)
                                                                <option value="{{ $w->id }}">{{ $w->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="flex gap-2 pt-1">
                                                    <button type="submit" class="btn-primary flex-1 py-2.5 text-sm">Simpan Perubahan</button>
                                                    <button type="button" @click="editOpen = false" class="px-4 py-2.5 text-sm rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors font-semibold">Batal</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($transactions->hasPages())
                <div class="px-6 py-4 flex items-center justify-between">
                    <p class="text-xs text-slate-400">
                        Menampilkan {{ $transactions->firstItem() }}-{{ $transactions->lastItem() }} dari {{ $transactions->total() }} transaksi
                    </p>
                    <div class="flex items-center gap-1">
                        @if($transactions->onFirstPage())
                            <span class="px-3 py-1.5 text-xs rounded-full bg-slate-100 dark:bg-slate-700 text-slate-300 dark:text-slate-500 cursor-not-allowed">← Sebelumnya</span>
                        @else
                            <a href="{{ $transactions->previousPageUrl() }}" class="px-3 py-1.5 text-xs rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-emerald-100 dark:hover:bg-emerald-900/30 hover:text-emerald-700 dark:hover:text-emerald-300 transition-colors font-semibold">← Sebelumnya</a>
                        @endif
                        <span class="px-3 py-1.5 text-xs font-bold text-emerald-600">{{ $transactions->currentPage() }} / {{ $transactions->lastPage() }}</span>
                        @if($transactions->hasMorePages())
                            <a href="{{ $transactions->nextPageUrl() }}" class="px-3 py-1.5 text-xs rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-emerald-100 dark:hover:bg-emerald-900/30 hover:text-emerald-700 dark:hover:text-emerald-300 transition-colors font-semibold">Berikutnya →</a>
                        @else
                            <span class="px-3 py-1.5 text-xs rounded-full bg-slate-100 dark:bg-slate-700 text-slate-300 dark:text-slate-500 cursor-not-allowed">Berikutnya →</span>
                        @endif
                    </div>
                </div>
                @endif
            @endif
        </div>
    </div>

    {{-- ═ DESKTOP: Kolom Kanan (Ghost Input + Habits) ═ --}}
    <div class="flex flex-col gap-6"
         x-data="{
                inputMode: 'reguler',
                inputText: '',
                walletId: '',
                toWalletId: '',
                amount: '',
                type: 'debt',
                descHutang: '',
                loading: false,
                lastResult: null,
                async submit() {
                    let payload = { inputMode: this.inputMode };
                    if (this.inputMode === 'reguler') {
                        if (!this.inputText.trim()) return;
                        payload.raw_text = this.inputText;
                        payload.wallet_id = this.walletId || null;
                    } else if (this.inputMode === 'mutasi') {
                        if (!this.amount || !this.walletId || !this.toWalletId) return;
                        payload.amount = this.amount;
                        payload.wallet_id = this.walletId;
                        payload.to_wallet_id = this.toWalletId;
                    } else if (this.inputMode === 'hutang') {
                        if (!this.descHutang || !this.amount || !this.walletId) return;
                        payload.desc_hutang = this.descHutang;
                        payload.amount = this.amount;
                        payload.type = this.type;
                        payload.wallet_id = this.walletId;
                    }

                    this.loading = true;
                    if(navigator.vibrate) navigator.vibrate([50]);

                    try {
                        const res = await fetch('{{ route('transactions.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify(payload)
                        });
                        
                        if (!res.ok) {
                            if (res.status === 422) {
                                const data = await res.json();
                                throw new Error(data.message || 'Validasi gagal.');
                            }
                            throw new Error('Server error (' + res.status + ')');
                        }
                        
                        const data = await res.json();
                        if (data.success) {
                            this.lastResult = data;
                            this.inputText = '';
                            this.descHutang = '';
                            this.amount = '';
                            setTimeout(() => { this.lastResult = null; window.location.reload(); }, 2000);
                        } else {
                            alertError(data.message || 'Gagal menyimpan transaksi.');
                        }
                    } catch(e) {
                        if (!navigator.onLine || e.message === 'Failed to fetch') {
                            if (typeof saveTransactionOffline === 'function') {
                                saveTransactionOffline(payload);
                                this.inputText = '';
                                this.descHutang = '';
                                this.amount = '';
                                if (typeof SobatSwal !== 'undefined') {
                                    SobatSwal.fire({
                                        icon: 'warning',
                                        title: 'Koneksi Terputus',
                                        text: 'Data disimpan offline dan akan disinkronkan otomatis.',
                                        timer: 3000,
                                        showConfirmButton: false
                                    });
                                }
                            }
                        } else {
                            alertError('Terjadi kesalahan jaringan. Coba lagi.');
                        }
                    }
                    this.loading = false;
                }
             }">
        <div class="flex flex-col gap-4 p-6 pl-0">
            <p class="section-title mb-3">⚡ Catat Cepat</p>

            {{-- Tabs --}}
            <div class="flex gap-1 mb-4 bg-slate-100 dark:bg-slate-800 p-1 rounded-xl">
                <button type="button" @click="inputMode = 'reguler'" :class="{'bg-white dark:bg-slate-700 shadow-sm text-emerald-600 dark:text-emerald-400': inputMode === 'reguler', 'text-slate-500': inputMode !== 'reguler'}" class="flex-1 py-2 text-xs font-bold rounded-lg transition-all">💸 Reguler</button>
                <button type="button" @click="inputMode = 'mutasi'" :class="{'bg-white dark:bg-slate-700 shadow-sm text-emerald-600 dark:text-emerald-400': inputMode === 'mutasi', 'text-slate-500': inputMode !== 'mutasi'}" class="flex-1 py-2 text-xs font-bold rounded-lg transition-all">🔄 Mutasi</button>
                <button type="button" @click="inputMode = 'hutang'" :class="{'bg-white dark:bg-slate-700 shadow-sm text-emerald-600 dark:text-emerald-400': inputMode === 'hutang', 'text-slate-500': inputMode !== 'hutang'}" class="flex-1 py-2 text-xs font-bold rounded-lg transition-all">🤝 Hutang</button>
            </div>

            {{-- Success flash --}}
            <div x-show="lastResult" x-cloak x-transition class="mb-3 p-3 rounded-2xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 text-sm font-semibold flex items-center gap-2">
                ✅ <span x-text="lastResult?.message"></span>
            </div>

            {{-- Mode: Reguler --}}
            <div x-show="inputMode === 'reguler'">
                <input
                    type="text"
                    x-model="inputText"
                    x-ref="desktopInput"
                    @keydown.enter="submit()"
                    :disabled="loading"
                    placeholder="kopi 15k | gaji 5000k"
                    class="input-field mb-3"
                    autofocus
                    id="ghost-input-desktop"
                />

                @if($wallets->isNotEmpty())
                <select x-model="walletId" class="input-field mb-3 text-sm">
                    <option value="">Pilih dompet (opsional)</option>
                    @foreach($wallets as $w)
                        <option value="{{ $w->id }}">{{ $w->icon }} {{ $w->name }}</option>
                    @endforeach
                </select>
                @endif
            </div>

            {{-- Mode: Mutasi --}}
            <div x-show="inputMode === 'mutasi'" x-cloak>
                <input type="number" x-model="amount" class="input-field mb-3" placeholder="Nominal (Rp)" min="0">
                <div class="grid grid-cols-2 gap-2 mb-3">
                    <select x-model="walletId" class="input-field text-sm">
                        <option value="">Dari Dompet...</option>
                        @foreach($wallets as $w)
                            <option value="{{ $w->id }}">{{ $w->name }}</option>
                        @endforeach
                    </select>
                    <select x-model="toWalletId" class="input-field text-sm">
                        <option value="">Ke Dompet...</option>
                        @foreach($wallets as $w)
                            <option value="{{ $w->id }}">{{ $w->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Mode: Hutang --}}
            <div x-show="inputMode === 'hutang'" x-cloak>
                <input type="text" x-model="descHutang" class="input-field mb-3" placeholder="Nama / Deskripsi Hutang">
                <input type="number" x-model="amount" class="input-field mb-3" placeholder="Nominal (Rp)" min="0">
                <div class="grid grid-cols-2 gap-2 mb-3">
                    <select x-model="type" class="input-field text-sm">
                        <option value="debt">Saya Berhutang</option>
                        <option value="repay_debt">Bayar Hutang (Keluar)</option>
                        <option disabled>-----------</option>
                        <option value="receivable">Beri Pinjaman</option>
                        <option value="collect_receivable">Terima Piutang (Masuk)</option>
                    </select>
                    <select x-model="walletId" class="input-field text-sm">
                        <option value="">Pilih Dompet...</option>
                        @foreach($wallets as $w)
                            <option value="{{ $w->id }}">{{ $w->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <button @click="submit()" :disabled="loading"
                    class="btn-primary w-full py-3 text-sm"
                    :class="{ 'pointer-events-none opacity-60': loading }">
                <svg x-show="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                <span x-text="loading ? 'Menyimpan...' : '✔ Simpan Transaksi'">✔ Simpan Transaksi</span>
            </button>
        </div>

        {{-- Quick Habits --}}
        @if($wallets->isNotEmpty())
        <div class="card p-5" x-show="inputMode === 'reguler'" x-cloak>
            <p class="section-title mb-3">⚡ Quick Habits</p>
            <div class="grid grid-cols-2 gap-2">
                @if($quickHabits->isEmpty())
                    <div class="col-span-2 text-center text-sm text-slate-400 py-4">
                        Belum ada Quick Habits.<br>Tambahkan di <a href="{{ route('app.dictionary') }}" class="text-emerald-500 font-semibold underline">Kamus</a> (maks. 5).
                    </div>
                @else
                    @foreach($quickHabits as $habit)
                    <button
                        @click="inputText = '{{ $habit->keyword }} '; $nextTick(() => $refs.desktopInput?.focus())"
                        type="button"
                        class="flex items-center gap-2 p-2.5 rounded-2xl hover:bg-emerald-50 dark:hover:bg-emerald-900/30 transition-all active:scale-95 text-left"
                    >
                        <span class="text-xl">{{ getHabitIcon($habit->keyword) }}</span>
                        <span class="text-sm font-semibold text-slate-600 dark:text-slate-400 truncate">{{ ucfirst($habit->keyword) }}</span>
                    </button>
                    @endforeach
                @endif
            </div>
        </div>
        @endif

        {{-- Top Kategori --}}
        @if($topCategories->isNotEmpty())
        <div class="card p-5">
            <p class="section-title mb-3">📊 Top Pengeluaran Bulan Ini</p>
            <div class="space-y-3">
                @php
                $colors = ['bg-rose-500', 'bg-amber-500', 'bg-blue-500'];
                $totalCat = $topCategories->sum('total');
                @endphp
                @foreach($topCategories as $i => $cat)
                @php $pct = $totalCat > 0 ? round(($cat->total / $totalCat) * 100) : 0; @endphp
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ $cat->category->name ?? 'Lain-lain' }}</span>
                        <span class="text-xs font-bold text-slate-500">{{ $pct }}%</span>
                    </div>
                    <div class="h-2 rounded-full bg-slate-100 dark:bg-slate-700">
                        <div class="h-2 rounded-full {{ $colors[$i] ?? 'bg-slate-400' }} transition-all duration-700" style="width: {{ $pct }}%"></div>
                    </div>
                    <p class="text-xs text-slate-400 mt-0.5">Rp {{ number_format($cat->total, 0, ',', '.') }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════════════
     MOBILE: Single Column Layout
═══════════════════════════════════════════════════════════════════════════ --}}
<div class="lg:hidden flex flex-col gap-4 px-4 pt-4 pb-24">

    {{-- Mobile Header --}}
    <div class="flex items-center justify-between pt-2">
        <div>
            <p class="text-sm text-slate-400">
                @if($streak > 0) <span class="text-amber-500 font-bold">🔥 {{ $streak }} hari</span> @endif
                &nbsp;|&nbsp; Aman hari ini: <span class="text-emerald-600 font-bold">Rp {{ number_format($safeToSpend, 0, ',', '.') }}</span>
            </p>
        </div>
        <p class="text-xs text-slate-400">{{ now()->translatedFormat('d M Y') }}</p>
    </div>

    {{-- Mobile Tabbed UI --}}
    <div x-data="{
            inputMode: 'reguler',
            inputText: '',
            walletId: '',
            toWalletId: '',
            amount: '',
            type: 'debt',
            descHutang: '',
            loading: false,
            lastResult: null,
            async submit() {
                let payload = { inputMode: this.inputMode };
                if (this.inputMode === 'reguler') {
                    if (!this.inputText.trim()) return;
                    payload.raw_text = this.inputText;
                    payload.wallet_id = this.walletId || null;
                } else if (this.inputMode === 'mutasi') {
                    if (!this.amount || !this.walletId || !this.toWalletId) return;
                    payload.amount = this.amount;
                    payload.wallet_id = this.walletId;
                    payload.to_wallet_id = this.toWalletId;
                } else if (this.inputMode === 'hutang') {
                    if (!this.descHutang || !this.amount || !this.walletId) return;
                    payload.desc_hutang = this.descHutang;
                    payload.amount = this.amount;
                    payload.type = this.type;
                    payload.wallet_id = this.walletId;
                }
                
                this.loading = true;
                if (navigator.vibrate) navigator.vibrate([50]);
                
                try {
                    const res = await fetch('{{ route('transactions.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(payload)
                    });

                    if (!res.ok) {
                        if (res.status === 422) {
                            const data = await res.json();
                            throw new Error(data.message || 'Validasi gagal.');
                        }
                        throw new Error('Server error (' + res.status + ')');
                    }

                    const data = await res.json();
                    if (data.success) {
                        this.lastResult = data;
                        this.inputText = '';
                        this.descHutang = '';
                        this.amount = '';
                        setTimeout(() => { this.lastResult = null; window.location.reload(); }, 2000);
                    } else {
                        alertError(data.message || 'Gagal menyimpan transaksi.');
                    }
                } catch(e) {
                    if (!navigator.onLine || e.message === 'Failed to fetch') {
                        if (typeof saveTransactionOffline === 'function') {
                            saveTransactionOffline(payload);
                            this.inputText = '';
                            this.descHutang = '';
                            this.amount = '';
                            if (typeof SobatSwal !== 'undefined') {
                                SobatSwal.fire({
                                    icon: 'warning',
                                    title: 'Koneksi Terputus',
                                    text: 'Data disimpan offline dan akan disinkronkan otomatis.',
                                    timer: 3000,
                                    showConfirmButton: false
                                });
                            }
                        }
                    } else {
                        alertError('Terjadi kesalahan jaringan. Coba lagi.');
                    }
                }
                this.loading = false;
            }
         }">
         
        {{-- Success flash --}}
        <div x-show="lastResult" x-cloak x-transition class="mb-3 p-3 rounded-2xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 text-sm font-bold flex items-center gap-2">
            ✅ <span x-text="lastResult?.message"></span>
        </div>

        {{-- Tabs --}}
        <div class="flex gap-1 mb-4 bg-slate-100 dark:bg-slate-800 p-1 rounded-xl">
            <button type="button" @click="inputMode = 'reguler'" :class="{'bg-white dark:bg-slate-700 shadow-sm text-emerald-600 dark:text-emerald-400': inputMode === 'reguler', 'text-slate-500': inputMode !== 'reguler'}" class="flex-1 py-2 text-xs font-bold rounded-lg transition-all">💸 Reguler</button>
            <button type="button" @click="inputMode = 'mutasi'" :class="{'bg-white dark:bg-slate-700 shadow-sm text-emerald-600 dark:text-emerald-400': inputMode === 'mutasi', 'text-slate-500': inputMode !== 'mutasi'}" class="flex-1 py-2 text-xs font-bold rounded-lg transition-all">🔄 Mutasi</button>
            <button type="button" @click="inputMode = 'hutang'" :class="{'bg-white dark:bg-slate-700 shadow-sm text-emerald-600 dark:text-emerald-400': inputMode === 'hutang', 'text-slate-500': inputMode !== 'hutang'}" class="flex-1 py-2 text-xs font-bold rounded-lg transition-all">🤝 Hutang</button>
        </div>

        {{-- Mode: Reguler --}}
        <div x-show="inputMode === 'reguler'">
            <input
                type="text"
                x-model="inputText"
                x-ref="mobileInput"
                @keydown.enter="submit()"
                :disabled="loading"
                placeholder="kopi 15k | gaji 5000k"
                class="input-field text-lg py-4 font-medium mb-2"
                autocomplete="off"
            />
            @if($wallets->isNotEmpty())
            <select x-model="walletId" class="input-field text-sm mb-2">
                <option value="">Pilih dompet (opsional)</option>
                @foreach($wallets as $w)
                    <option value="{{ $w->id }}">{{ $w->icon }} {{ $w->name }}</option>
                @endforeach
            </select>
            @endif
        </div>

        {{-- Mode: Mutasi --}}
        <div x-show="inputMode === 'mutasi'" x-cloak>
            <div class="mb-3">
                <input type="number" x-model="amount" class="input-field py-3" placeholder="Nominal (Rp)" min="0">
            </div>
            <div class="grid grid-cols-2 gap-2 mb-2">
                <div>
                    <select x-model="walletId" class="input-field text-sm">
                        <option value="">Dari Dompet...</option>
                        @foreach($wallets as $w)
                            <option value="{{ $w->id }}">{{ $w->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select x-model="toWalletId" class="input-field text-sm">
                        <option value="">Ke Dompet...</option>
                        @foreach($wallets as $w)
                            <option value="{{ $w->id }}">{{ $w->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Mode: Hutang --}}
        <div x-show="inputMode === 'hutang'" x-cloak>
            <div class="mb-3">
                <input type="text" x-model="descHutang" class="input-field" placeholder="Nama / Deskripsi">
            </div>
            <div class="mb-3">
                <input type="number" x-model="amount" class="input-field py-3" placeholder="Nominal (Rp)" min="0">
            </div>
            <div class="grid grid-cols-2 gap-2 mb-2">
                <div>
                    <select x-model="type" class="input-field text-sm">
                        <option value="debt">Saya Berhutang</option>
                        <option value="repay_debt">Bayar Hutang (Keluar)</option>
                        <option disabled>-----------</option>
                        <option value="receivable">Beri Pinjaman</option>
                        <option value="collect_receivable">Terima Piutang (Masuk)</option>
                    </select>
                </div>
                <div>
                    <select x-model="walletId" class="input-field text-sm">
                        <option value="">Pilih Dompet...</option>
                        @foreach($wallets as $w)
                            <option value="{{ $w->id }}">{{ $w->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <button @click="submit()" :disabled="loading"
                class="btn-primary w-full py-4 mt-1 text-base"
                :class="{ 'pointer-events-none opacity-60': loading }">
            <svg x-show="loading" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
            <span x-text="loading ? 'Menyimpan...' : '⚡ Catat Sekarang'">⚡ Catat Sekarang</span>
        </button>

        {{-- Mobile Quick Habits --}}
        <div class="flex gap-2 overflow-x-auto pb-1 mt-6 scrollbar-hide" x-show="inputMode === 'reguler'" x-transition x-cloak>
            @if($quickHabits->isEmpty())
                <div class="w-full text-center text-xs text-slate-400 py-2">
                    Tambahkan Quick Habits di <a href="{{ route('app.dictionary') }}" class="text-emerald-500 font-semibold underline">Kamus</a>.
                </div>
            @else
                @foreach($quickHabits as $h)
                <button
                    @click="inputText = '{{ $h->keyword }} '; $nextTick(() => { $refs.mobileInput?.focus(); })"
                    type="button"
                    class="flex flex-col items-center gap-1 p-2.5 rounded-2xl bg-white dark:bg-slate-800 shadow-sm shrink-0 active:scale-95 transition-transform"
                    style="min-width: 64px;"
                >
                    <span class="text-2xl">{{ getHabitIcon($h->keyword) }}</span>
                    <span class="text-xs font-semibold text-slate-600 dark:text-slate-400 w-full truncate text-center">{{ ucfirst($h->keyword) }}</span>
                </button>
                @endforeach
            @endif
        </div>
    </div>

    {{-- Mobile: Recent Transactions (3 terbaru) --}}
    <div>
        <p class="section-title mb-3">Transaksi Terbaru</p>
        @if($transactions->isEmpty())
            <div class="text-center py-8 text-slate-400">
                <p class="text-3xl mb-2">📝</p>
                <p class="text-sm">Belum ada transaksi. Mulai catat sekarang!</p>
            </div>
        @else
            <div class="space-y-2">
                @foreach($transactions->take(5) as $trx)
                <div class="card p-3.5 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <div class="w-9 h-9 rounded-2xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-lg shrink-0">
                            {{ $trx->type === 'income' ? '💰' : '💸' }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-slate-800 dark:text-slate-200 capitalize truncate">{{ $trx->raw_text }}</p>
                            <p class="text-xs text-slate-400">{{ $trx->category->name ?? 'Lain-lain' }} · {{ $trx->created_at->format('H:i') }}</p>
                        </div>
                    </div>
                    <p class="text-sm font-bold {{ $trx->type === 'income' ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-800 dark:text-slate-200' }} shrink-0">
                        {{ $trx->type === 'income' ? '+' : '-' }}Rp {{ number_format($trx->amount, 0, ',', '.') }}
                    </p>
                </div>
                @endforeach
            </div>
        @endif
    </div>

</div>

</x-layouts.app>
<script>
    // Offline Queue with IndexedDB
    const dbName = 'SobatSaldoDB';
    let db;
    const request = indexedDB.open(dbName, 1);
    
    request.onupgradeneeded = (event) => {
        db = event.target.result;
        if (!db.objectStoreNames.contains('transactions')) {
            db.createObjectStore('transactions', { autoIncrement: true });
        }
    };
    
    request.onsuccess = (event) => {
        db = event.target.result;
        window.addEventListener('online', syncOfflineTransactions);
        syncOfflineTransactions();
    };

    function saveTransactionOffline(payload) {
        if (!db) return;
        const tx = db.transaction('transactions', 'readwrite');
        const store = tx.objectStore('transactions');
        store.add(payload);
    }

    async function syncOfflineTransactions() {
        if (!navigator.onLine || !db) return;
        const tx = db.transaction('transactions', 'readonly');
        const store = tx.objectStore('transactions');
        const request = store.openCursor();
        let successCount = 0;
        
        request.onsuccess = async (event) => {
            const cursor = event.target.result;
            if (cursor) {
                try {
                    const res = await fetch('/transactions', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(cursor.value)
                    });
                    
                    // Jika sukses atau validasi gagal permanen (misal 422), hapus dari offline queue
                    if (res.ok || res.status === 422) {
                        const txDel = db.transaction('transactions', 'readwrite');
                        txDel.objectStore('transactions').delete(cursor.key);
                        if (res.ok) successCount++;
                    }
                } catch (e) {
                    console.error('Offline sync failed for one item:', e);
                }
                cursor.continue();
            } else {
                // Selesai looping
                if (successCount > 0) {
                    if (typeof SobatSwal !== 'undefined') {
                        SobatSwal.fire({
                            icon: 'success',
                            title: 'Sinkronisasi Berhasil',
                            text: successCount + ' transaksi offline telah disinkronkan.',
                            timer: 2500,
                            showConfirmButton: false
                        }).then(() => window.location.reload());
                    } else {
                        alert(successCount + ' transaksi offline telah disinkronkan.');
                        window.location.reload();
                    }
                }
            }
        };
    }
</script>
