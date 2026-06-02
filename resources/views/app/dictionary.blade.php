<x-layouts.app title="Kamus Kata Kunci" metaDescription="Kelola kata kunci untuk kategorisasi otomatis transaksi.">

{{-- Flash Messages --}}
@if (session('success'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
     x-transition class="fixed top-4 right-4 z-50 p-4 rounded-2xl bg-emerald-500 text-white shadow-xl max-w-sm text-sm font-semibold flex items-center gap-2">
    ✅ {{ session('success') }}
</div>
@endif
@if (session('error'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
     x-transition class="fixed top-4 right-4 z-50 p-4 rounded-2xl bg-rose-500 text-white shadow-xl max-w-sm text-sm font-semibold flex items-center gap-2">
    ⚠️ {{ session('error') }}
</div>
@endif

<div class="max-w-3xl mx-auto px-4 py-6 lg:py-8 pb-24 lg:pb-8"
     x-data="{
        showAddForm: false,
        editId: null,
        editKeyword: '',
        editCategoryId: '',
        openEdit(id, keyword, catId) {
            this.editId = id;
            this.editKeyword = keyword;
            this.editCategoryId = String(catId);
        },
        closeEdit() {
            this.editId = null;
            this.editKeyword = '';
            this.editCategoryId = '';
        }
     }">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-4 flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Kamus Kata Kunci 📖</h1>
            <p class="text-sm text-slate-400 mt-0.5">{{ $keywords->total() }} kata kunci terdaftar</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('app.categories') }}" class="btn-secondary px-4 py-2.5 text-sm flex items-center gap-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold rounded-xl transition-colors">
                🏷️ Kelola Kategori
            </a>
            <button @click="showAddForm = !showAddForm"
                    class="btn-primary px-4 py-2.5 text-sm flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Kata
            </button>
        </div>
    </div>

    {{-- Tambah Kata Form --}}
    <div x-show="showAddForm" x-cloak x-transition class="card p-5 mb-5">
        <div class="flex items-center justify-between mb-4">
            <p class="font-bold text-slate-800 dark:text-white">Tambah Kata Kunci Baru</p>
            <button @click="showAddForm = false" class="p-1 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition-all">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        @if ($errors->any())
        <div class="p-3 mb-3 rounded-xl bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 text-sm">
            @foreach ($errors->all() as $error) <p>• {{ $error }}</p> @endforeach
        </div>
        @endif
        <form method="POST" action="{{ route('dictionary.store') }}" class="space-y-3">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="section-title block mb-1">Kata Kunci</label>
                    <input type="text" name="keyword" value="{{ old('keyword') }}" placeholder="contoh: kopi, gojek, spp..." class="input-field" required autocomplete="off" />
                </div>
                <div>
                    <label class="section-title block mb-1">Kategori</label>
                    <select name="category_id" class="input-field" required>
                        <option value="">Pilih kategori...</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn-primary flex-1 py-2.5 text-sm">💾 Tambahkan ke Kamus</button>
                <button type="button" @click="showAddForm = false" class="px-4 py-2.5 text-sm rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors font-semibold">Batal</button>
            </div>
        </form>
    </div>

    {{-- Search + Filter --}}
    <form method="GET" action="{{ route('app.dictionary') }}" class="flex gap-2 mb-5 flex-wrap">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kata kunci..."
               class="input-field flex-1 min-w-[180px]" />
        <select name="category_id" class="input-field">
            <option value="">Semua Kategori</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn-primary px-4 py-2.5 text-sm">Cari</button>
        @if(request()->hasAny(['search','category_id']))
            <a href="{{ route('app.dictionary') }}" class="px-4 py-2.5 text-sm text-slate-500 hover:text-rose-500 transition-colors font-medium rounded-full self-center">Reset</a>
        @endif
    </form>

    {{-- Keywords List --}}
    @if($keywords->isEmpty())
    <div class="card p-10 text-center">
        <p class="text-4xl mb-3">📖</p>
        <p class="text-slate-500 dark:text-slate-400 font-semibold">Belum ada kata kunci ditemukan.</p>
        <p class="text-slate-400 text-sm mt-1">Tambahkan kata kunci untuk mengajarkan sistem cara mengkategorikan transaksimu.</p>
    </div>
    @else
    <div class="card overflow-hidden">
        <div class="divide-y divide-slate-50 dark:divide-slate-700/30">
            @foreach($keywords as $kw)
            <div class="flex items-center justify-between px-5 py-3.5 hover:bg-slate-50/70 dark:hover:bg-slate-700/20 transition-colors group">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-8 h-8 rounded-2xl bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center text-sm font-bold text-emerald-600 dark:text-emerald-400 shrink-0">
                        {{ strtoupper(substr($kw->keyword, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-200 truncate">{{ $kw->keyword }}</p>
                        <p class="text-xs text-slate-400">{{ $kw->category->name ?? '-' }}
                            @if($kw->is_quick_habit) <span class="ml-1 text-amber-500 font-bold" title="Quick Habit">⚡</span> @endif
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-1 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity shrink-0">
                    <form method="POST" action="{{ route('dictionary.quick-habit', $kw->id) }}">
                        @csrf @method('PATCH')
                        <button type="submit" class="p-1.5 rounded-xl transition-all {{ $kw->is_quick_habit ? 'text-amber-500 bg-amber-50 dark:bg-amber-900/30 hover:bg-slate-100 dark:hover:bg-slate-700' : 'text-slate-400 hover:text-amber-500 hover:bg-amber-50 dark:hover:bg-amber-900/30' }}" title="{{ $kw->is_quick_habit ? 'Hapus dari Quick Habits' : 'Jadikan Quick Habit' }}">
                            <svg class="w-4 h-4" fill="{{ $kw->is_quick_habit ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </button>
                    </form>
                    <button @click="openEdit({{ $kw->id }}, '{{ addslashes($kw->keyword) }}', {{ $kw->category_id }})"
                            class="p-1.5 rounded-xl text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 transition-all" title="Edit">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </button>
                    <form x-data method="POST" action="{{ route('dictionary.destroy', $kw->id) }}"
                          @submit.prevent="confirmDelete('Kata kunci \'{{ addslashes($kw->keyword) }}\' akan dihapus permanen.').then(ok => { if (ok) $el.submit(); })">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-1.5 rounded-xl text-slate-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/30 transition-all" title="Hapus">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
        {{-- Pagination --}}
        @if($keywords->hasPages())
        <div class="px-5 py-4 flex items-center justify-between">
            <p class="text-xs text-slate-400">{{ $keywords->firstItem() }}-{{ $keywords->lastItem() }} dari {{ $keywords->total() }}</p>
            <div class="flex items-center gap-1">
                @if(!$keywords->onFirstPage())
                    <a href="{{ $keywords->previousPageUrl() }}" class="px-3 py-1.5 text-xs rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-emerald-100 dark:hover:bg-emerald-900/30 transition-colors font-semibold">← Prev</a>
                @endif
                <span class="px-3 py-1.5 text-xs font-bold text-emerald-600">{{ $keywords->currentPage() }} / {{ $keywords->lastPage() }}</span>
                @if($keywords->hasMorePages())
                    <a href="{{ $keywords->nextPageUrl() }}" class="px-3 py-1.5 text-xs rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-emerald-100 dark:hover:bg-emerald-900/30 transition-colors font-semibold">Next →</a>
                @endif
            </div>
        </div>
        @endif
    </div>
    @endif

    {{-- Edit Modal --}}
    <div x-show="editId !== null" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm"
         @click.self="closeEdit()">
        <div class="card p-6 w-full max-w-sm shadow-2xl" @click.outside="closeEdit()">
            <div class="flex items-center justify-between mb-4">
                <p class="font-bold text-slate-800 dark:text-white">Edit Kata Kunci</p>
                <button @click="closeEdit()" class="p-1 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition-all">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <template x-if="editId !== null">
                <form :action="`/dictionary/${editId}`" method="POST" class="space-y-3">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="section-title block mb-1">Kata Kunci</label>
                        <input type="text" name="keyword" x-model="editKeyword" class="input-field" required />
                    </div>
                    <div>
                        <label class="section-title block mb-1">Kategori</label>
                        <select name="category_id" class="input-field" required>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" :selected="editCategoryId === '{{ $cat->id }}'">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex gap-2 pt-1">
                        <button type="submit" class="btn-primary flex-1 py-2.5 text-sm">💾 Simpan</button>
                        <button type="button" @click="closeEdit()" class="px-4 py-2.5 text-sm rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 font-semibold">Batal</button>
                    </div>
                </form>
            </template>
        </div>
    </div>

</div>

</x-layouts.app>
