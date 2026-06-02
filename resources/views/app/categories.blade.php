<x-layouts.app title="Kelola Kategori" metaDescription="Kelola kategori transaksi pemasukan dan pengeluaranmu.">

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
        editName: '',
        editType: '',
        openEdit(id, name, type) {
            this.editId = id;
            this.editName = name;
            this.editType = type;
        },
        closeEdit() {
            this.editId = null;
            this.editName = '';
            this.editType = '';
        }
     }">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-4 flex-wrap gap-3">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('app.dictionary') }}" class="text-slate-400 hover:text-emerald-500 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Kelola Kategori 🏷️</h1>
            </div>
            <p class="text-sm text-slate-400">{{ $categories->total() }} kategori terdaftar</p>
        </div>
        <div class="flex gap-2">
            <button @click="showAddForm = !showAddForm"
                    class="btn-primary px-4 py-2.5 text-sm flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Kategori
            </button>
        </div>
    </div>

    {{-- Tambah Kategori Form --}}
    <div x-show="showAddForm" x-cloak x-transition class="card p-5 mb-5">
        <div class="flex items-center justify-between mb-4">
            <p class="font-bold text-slate-800 dark:text-white">Tambah Kategori Baru</p>
            <button @click="showAddForm = false" class="p-1 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition-all">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        @if ($errors->any())
        <div class="p-3 mb-3 rounded-xl bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 text-sm">
            @foreach ($errors->all() as $error) <p>• {{ $error }}</p> @endforeach
        </div>
        @endif
        <form method="POST" action="{{ route('categories.store') }}" class="space-y-3">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="section-title block mb-1">Nama Kategori</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="contoh: Gaji, Makanan, Transportasi..." class="input-field" required autocomplete="off" />
                </div>
                <div>
                    <label class="section-title block mb-1">Jenis Kategori</label>
                    <select name="type" class="input-field" required>
                        <option value="expense" {{ old('type') == 'expense' ? 'selected' : '' }}>Pengeluaran</option>
                        <option value="income" {{ old('type') == 'income' ? 'selected' : '' }}>Pemasukan</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn-primary flex-1 py-2.5 text-sm">💾 Simpan Kategori</button>
                <button type="button" @click="showAddForm = false" class="px-4 py-2.5 text-sm rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors font-semibold">Batal</button>
            </div>
        </form>
    </div>

    {{-- Search + Filter --}}
    <form method="GET" action="{{ route('app.categories') }}" class="flex gap-2 mb-5 flex-wrap">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kategori..."
               class="input-field flex-1 min-w-[180px]" />
        <select name="type" class="input-field">
            <option value="">Semua Jenis</option>
            <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>Pengeluaran</option>
            <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>Pemasukan</option>
        </select>
        <button type="submit" class="btn-primary px-4 py-2.5 text-sm">Cari</button>
        @if(request()->hasAny(['search','type']))
            <a href="{{ route('app.categories') }}" class="px-4 py-2.5 text-sm text-slate-500 hover:text-rose-500 transition-colors font-medium rounded-full self-center">Reset</a>
        @endif
    </form>

    {{-- Categories List --}}
    @if($categories->isEmpty())
    <div class="card p-10 text-center">
        <p class="text-4xl mb-3">🏷️</p>
        <p class="text-slate-500 dark:text-slate-400 font-semibold">Belum ada kategori ditemukan.</p>
    </div>
    @else
    <div class="card overflow-hidden">
        <div class="divide-y divide-slate-50 dark:divide-slate-700/30">
            @foreach($categories as $cat)
            <div class="flex items-center justify-between px-5 py-3.5 hover:bg-slate-50/70 dark:hover:bg-slate-700/20 transition-colors group">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-8 h-8 rounded-2xl bg-{{ $cat->type == 'income' ? 'emerald' : 'rose' }}-50 dark:bg-{{ $cat->type == 'income' ? 'emerald' : 'rose' }}-900/30 flex items-center justify-center text-sm font-bold text-{{ $cat->type == 'income' ? 'emerald' : 'rose' }}-600 dark:text-{{ $cat->type == 'income' ? 'emerald' : 'rose' }}-400 shrink-0">
                        {{ strtoupper(substr($cat->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-200 truncate">{{ $cat->name }}</p>
                        <p class="text-xs text-slate-400">
                            {{ $cat->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                            @if($cat->is_default) <span class="ml-1 text-slate-500 font-bold" title="Kategori Default">• Bawaan Sistem</span> @endif
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-1 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity shrink-0">
                    @if(!$cat->is_default)
                    <button @click="openEdit({{ $cat->id }}, '{{ addslashes($cat->name) }}', '{{ $cat->type }}')"
                            class="p-1.5 rounded-xl text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 transition-all" title="Edit">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </button>
                    <form x-data method="POST" action="{{ route('categories.destroy', $cat->id) }}"
                          @submit.prevent="confirmDelete('Kategori \'{{ addslashes($cat->name) }}\' akan dihapus permanen.').then(ok => { if (ok) $el.submit(); })">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-1.5 rounded-xl text-slate-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/30 transition-all" title="Hapus">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </form>
                    @else
                    <span class="text-xs text-slate-400 italic px-2">Tidak bisa diedit</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        {{-- Pagination --}}
        @if($categories->hasPages())
        <div class="p-4 border-t border-slate-100 dark:border-slate-700/50">
            {{ $categories->links('pagination::tailwind') }}
        </div>
        @endif
    </div>
    @endif

    {{-- Edit Modal --}}
    <div x-show="editId !== null" x-cloak
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div class="bg-white dark:bg-slate-800 rounded-3xl w-full max-w-sm overflow-hidden shadow-2xl ring-1 ring-slate-900/5"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             @click.outside="closeEdit">
            
            <div class="p-5 border-b border-slate-100 dark:border-slate-700/50 flex justify-between items-center">
                <h3 class="font-bold text-slate-800 dark:text-white">Edit Kategori</h3>
                <button @click="closeEdit" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-5">
                <form method="POST" :action="`/categories/${editId}`" class="space-y-3">
                    @csrf @method('PUT')
                    <div>
                        <label class="section-title block mb-1">Nama Kategori</label>
                        <input type="text" name="name" x-model="editName" class="input-field" required autocomplete="off" />
                    </div>
                    <div>
                        <label class="section-title block mb-1">Jenis Kategori</label>
                        <select name="type" x-model="editType" class="input-field" required>
                            <option value="expense">Pengeluaran</option>
                            <option value="income">Pemasukan</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-primary w-full py-2.5 text-sm mt-2">Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>
</div>

</x-layouts.app>
