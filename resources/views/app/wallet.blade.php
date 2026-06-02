<x-layouts.app title="Dompet" metaDescription="Kelola semua dompet keuanganmu dalam satu tempat.">

{{-- Flash Messages --}}
@if (session('success'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
     x-transition class="fixed top-4 right-4 z-50 p-4 rounded-2xl bg-emerald-500 text-white shadow-xl max-w-sm text-sm font-semibold flex items-center gap-2">
    ✅ {{ session('success') }}
</div>
@endif
@if (session('error'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)"
     x-transition class="fixed top-4 right-4 z-50 p-4 rounded-2xl bg-rose-500 text-white shadow-xl max-w-xs text-sm font-semibold flex items-center gap-2">
    ⚠️ {{ session('error') }}
</div>
@endif

<div class="max-w-3xl mx-auto px-4 py-6 lg:py-8 pb-24 lg:pb-8"
     x-data="{
        showAddForm: false,
        showEditId: null,
        showDeleteId: null,
        editData: {},
        openEdit(wallet) {
            this.editData = { ...wallet };
            this.showEditId = wallet.id;
        }
     }">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Dompetku 💳</h1>
            <p class="text-sm text-slate-400 mt-0.5">Total aset: <span class="text-emerald-600 font-bold">Rp {{ number_format($totalBalance, 0, ',', '.') }}</span></p>
        </div>
        <button @click="showAddForm = !showAddForm"
                class="btn-primary px-4 py-2.5 text-sm flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Dompet
        </button>
    </div>

    {{-- Add Wallet Form --}}
    <div x-show="showAddForm" x-cloak x-transition class="card p-5 mb-6">
        <div class="flex items-center justify-between mb-4">
            <p class="font-bold text-slate-800 dark:text-white">Tambah Dompet Baru</p>
            <button @click="showAddForm = false" class="p-1 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition-all">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('wallets.store') }}" class="space-y-3">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="section-title block mb-1">Nama Dompet</label>
                    <input type="text" name="name" placeholder="BCA, GoPay, Cash..." class="input-field" required />
                </div>
                <div>
                    <label class="section-title block mb-1">Jenis</label>
                    <select name="type" class="input-field">
                        <option value="bank">🏦 Bank</option>
                        <option value="ewallet">📱 E-Wallet</option>
                        <option value="cash">💵 Cash</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="section-title block mb-1">No. Rekening (opsional)</label>
                    <input type="text" name="account_number" placeholder="xxxx xxxx xxxx" class="input-field" />
                </div>
                <div>
                    <label class="section-title block mb-1">Warna Kartu</label>
                    <select name="color_theme" class="input-field">
                        <option value="emerald">🟢 Hijau (Emerald)</option>
                        <option value="blue">🔵 Biru</option>
                        <option value="amber">🟡 Kuning (Amber)</option>
                        <option value="rose">🔴 Merah (Rose)</option>
                        <option value="violet">🟣 Ungu</option>
                        <option value="slate">⚫ Abu-abu</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-2 pt-1">
                <button type="submit" class="btn-primary flex-1 py-2.5 text-sm">💾 Simpan Dompet</button>
                <button type="button" @click="showAddForm = false"
                        class="px-4 py-2.5 text-sm rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors font-semibold">Batal</button>
            </div>
        </form>
    </div>

    {{-- Wallet Cards --}}
    @if($wallets->isEmpty())
        <div class="card p-12 text-center">
            <p class="text-5xl mb-4">💳</p>
            <p class="text-slate-600 dark:text-slate-400 font-semibold mb-2">Belum ada dompet</p>
            <p class="text-slate-400 text-sm mb-4">Tambahkan dompet pertamamu untuk mulai mencatat transaksi!</p>
            <button @click="showAddForm = true" class="btn-primary px-6 py-2.5 text-sm">+ Tambah Dompet</button>
        </div>
    @else
        <div class="space-y-4">
            @foreach($wallets as $wallet)
            <div class="relative" x-data="{ menuOpen: false }">
                {{-- ATM Card --}}
                <div class="relative h-40 rounded-3xl bg-gradient-to-br {{ $wallet->gradient }} p-5 shadow-xl {{ $wallet->shadow_color }} overflow-hidden">
                    {{-- Background pattern --}}
                    <div class="absolute -right-8 -top-8 w-48 h-48 rounded-full bg-white/10"></div>
                    <div class="absolute -right-4 bottom-0 w-32 h-32 rounded-full bg-white/5"></div>

                    <div class="relative z-10 flex justify-between items-start h-full">
                        <div class="flex flex-col justify-between h-full">
                            <div>
                                <p class="text-white/70 text-xs font-semibold uppercase tracking-widest">{{ ['bank' => 'Bank Account', 'ewallet' => 'E-Wallet', 'cash' => 'Cash'][$wallet->type] }}</p>
                                <p class="text-white text-lg font-bold mt-0.5">{{ $wallet->icon }} {{ $wallet->name }}</p>
                                @if($wallet->account_number)
                                    <p class="text-white/50 text-xs mt-1 font-mono">{{ $wallet->account_number }}</p>
                                @endif
                            </div>
                            <div>
                                <p class="text-white/60 text-xs uppercase tracking-wide font-semibold">Saldo Bersih</p>
                                <p class="text-white text-2xl font-bold">Rp {{ number_format($wallet->balance, 0, ',', '.') }}</p>
                            </div>
                        </div>

                        {{-- Actions menu --}}
                        <div class="relative">
                            <button @click="menuOpen = !menuOpen" @click.outside="menuOpen = false"
                                    class="p-2 rounded-2xl bg-white/20 hover:bg-white/30 transition-colors text-white">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                </svg>
                            </button>
                            <div x-show="menuOpen" x-cloak x-transition class="absolute right-0 top-10 z-20 bg-white dark:bg-slate-800 rounded-2xl shadow-xl overflow-hidden min-w-[140px]">
                                <button @click="openEdit({ id: {{ $wallet->id }}, name: '{{ addslashes($wallet->name) }}', type: '{{ $wallet->type }}', color_theme: '{{ $wallet->color_theme }}', account_number: '{{ addslashes($wallet->account_number ?? '') }}' }); menuOpen = false"
                                        class="flex items-center gap-2 w-full px-4 py-3 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                                    <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Edit
                                </button>
                                <button @click="showDeleteId = {{ $wallet->id }}; menuOpen = false"
                                        class="flex items-center gap-2 w-full px-4 py-3 text-sm text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/30 transition-colors">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Delete Confirm Overlay --}}
                <div x-show="showDeleteId === {{ $wallet->id }}" x-cloak
                     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm"
                     @click.self="showDeleteId = null">
                    <div class="card p-6 max-w-sm w-full shadow-2xl" @click.outside="showDeleteId = null">
                        <div class="text-center mb-4">
                            <p class="text-4xl mb-2">🗑️</p>
                            <p class="font-bold text-slate-800 dark:text-white">Hapus Dompet?</p>
                            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                                Yakin ingin menghapus dompet <strong>{{ $wallet->name }}</strong>?
                                @php $trxCount = $wallet->transactions()->count(); @endphp
                                @if($trxCount > 0)
                                    <br><span class="text-amber-600">Dompet ini memiliki {{ $trxCount }} transaksi.</span>
                                @endif
                            </p>
                        </div>

                        @if($trxCount > 0)
                        <div class="mb-4">
                            <label class="section-title block mb-1">Pindahkan transaksi ke:</label>
                            <form method="POST" action="{{ route('wallets.destroy', $wallet->id) }}" id="delete-wallet-{{ $wallet->id }}">
                                @csrf @method('DELETE')
                                <select name="reassign_to" class="input-field mb-3">
                                    <option value="">Biarkan tanpa dompet</option>
                                    @foreach($wallets->where('id', '!=', $wallet->id) as $other)
                                        <option value="{{ $other->id }}">{{ $other->name }}</option>
                                    @endforeach
                                </select>
                                <div class="flex gap-2">
                                    <button type="submit" class="flex-1 py-2.5 text-sm rounded-full bg-rose-500 hover:bg-rose-600 text-white font-bold transition-colors">Hapus & Pindahkan</button>
                                    <button type="button" @click="showDeleteId = null" class="px-4 py-2.5 text-sm rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 font-semibold">Batal</button>
                                </div>
                            </form>
                        </div>
                        @else
                        <form method="POST" action="{{ route('wallets.destroy', $wallet->id) }}" class="flex gap-2">
                            @csrf @method('DELETE')
                            <button type="submit" class="flex-1 py-2.5 text-sm rounded-full bg-rose-500 hover:bg-rose-600 text-white font-bold transition-colors">Ya, Hapus</button>
                            <button type="button" @click="showDeleteId = null" class="px-4 py-2.5 text-sm rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 font-semibold">Batal</button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Edit Wallet Modal --}}
        <div x-show="showEditId !== null" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm"
             @click.self="showEditId = null">
            <div class="card p-6 w-full max-w-md shadow-2xl" @click.outside="showEditId = null">
                <div class="flex items-center justify-between mb-4">
                    <p class="font-bold text-slate-800 dark:text-white">Edit Dompet</p>
                    <button @click="showEditId = null" class="p-1 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition-all">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <template x-if="showEditId !== null">
                    <form :action="`/wallet/${showEditId}`" method="POST" class="space-y-3">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="section-title block mb-1">Nama Dompet</label>
                                <input type="text" name="name" :value="editData.name" class="input-field" required />
                            </div>
                            <div>
                                <label class="section-title block mb-1">Jenis</label>
                                <select name="type" class="input-field">
                                    <option value="bank"    :selected="editData.type === 'bank'">🏦 Bank</option>
                                    <option value="ewallet" :selected="editData.type === 'ewallet'">📱 E-Wallet</option>
                                    <option value="cash"    :selected="editData.type === 'cash'">💵 Cash</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="section-title block mb-1">No. Rekening</label>
                                <input type="text" name="account_number" :value="editData.account_number" class="input-field" />
                            </div>
                            <div>
                                <label class="section-title block mb-1">Warna Kartu</label>
                                <select name="color_theme" class="input-field">
                                    <option value="emerald" :selected="editData.color_theme === 'emerald'">🟢 Hijau</option>
                                    <option value="blue"    :selected="editData.color_theme === 'blue'">🔵 Biru</option>
                                    <option value="amber"   :selected="editData.color_theme === 'amber'">🟡 Amber</option>
                                    <option value="rose"    :selected="editData.color_theme === 'rose'">🔴 Rose</option>
                                    <option value="violet"  :selected="editData.color_theme === 'violet'">🟣 Ungu</option>
                                    <option value="slate"   :selected="editData.color_theme === 'slate'">⚫ Abu-abu</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex gap-2 pt-1">
                            <button type="submit" class="btn-primary flex-1 py-2.5 text-sm">💾 Simpan Perubahan</button>
                            <button type="button" @click="showEditId = null" class="px-4 py-2.5 text-sm rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 font-semibold">Batal</button>
                        </div>
                    </form>
                </template>
            </div>
        </div>

        {{-- Recent Wallet Activity --}}
        @if($activities->isNotEmpty())
        <div class="card mt-6 overflow-hidden">
            <div class="px-5 py-4">
                <p class="section-title">Aktivitas Terbaru</p>
            </div>
            <div class="divide-y divide-slate-50 dark:divide-slate-700/30">
                @foreach($activities as $act)
                <div class="flex items-center justify-between px-5 py-3.5 hover:bg-slate-50/70 dark:hover:bg-slate-700/20 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-2xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-base shrink-0">
                            {{ $act->type === 'income' ? '💰' : '💸' }}
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-800 dark:text-slate-200 capitalize">{{ $act->raw_text }}</p>
                            <p class="text-xs text-slate-400">{{ $act->wallet->name ?? '-' }} · {{ $act->created_at->translatedFormat('d M, H:i') }}</p>
                        </div>
                    </div>
                    <p class="text-sm font-bold {{ $act->type === 'income' ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-700 dark:text-slate-300' }} shrink-0">
                        {{ $act->type === 'income' ? '+' : '-' }}Rp {{ number_format($act->amount, 0, ',', '.') }}
                    </p>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    @endif

</div>

</x-layouts.app>
