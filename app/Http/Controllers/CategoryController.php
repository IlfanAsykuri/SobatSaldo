<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\KeywordDictionary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    // ─── Index ────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        try {
            $userId = Auth::id();
            
            $query = Category::where('user_id', $userId)->orderBy('name');

            if ($request->filled('search')) {
                $query->where('name', 'like', '%' . e($request->search) . '%');
            }

            if ($request->filled('type') && in_array($request->type, ['income', 'expense'])) {
                $query->where('type', $request->type);
            }

            $categories = $query->paginate(15)->withQueryString();

            return view('app.categories', compact('categories'));
        } catch (\Throwable $e) {
            return back()->with('error', 'Terjadi kesalahan saat memuat kategori.');
        }
    }

    // ─── Store ────────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'type' => ['required', 'in:income,expense'],
        ]);

        try {
            Category::create([
                'user_id' => Auth::id(),
                'name'    => trim($request->name),
                'type'    => $request->type,
                'is_default' => false,
            ]);

            return redirect()->route('app.categories')
                ->with('success', "Kategori '{$request->name}' berhasil ditambahkan! 🏷️");
        } catch (\Throwable $e) {
            return back()->with('error', 'Terjadi kesalahan saat menyimpan kategori.')->withInput();
        }
    }

    // ─── Update ───────────────────────────────────────────────────────────────

    public function update(Request $request, int $id)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'type' => ['required', 'in:income,expense'],
        ]);

        try {
            $category = Category::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $category->update([
                'name' => trim($request->name),
                'type' => $request->type,
            ]);

            return redirect()->route('app.categories')
                ->with('success', 'Kategori berhasil diperbarui! ✅');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->with('error', 'Kategori tidak ditemukan atau kamu tidak memiliki izin.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Terjadi kesalahan saat memperbarui kategori.');
        }
    }

    // ─── Destroy ──────────────────────────────────────────────────────────────

    public function destroy(int $id)
    {
        try {
            $category = Category::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            // Proteksi: jangan hapus jika is_default
            if ($category->is_default) {
                return back()->with('error', 'Kategori default (Lain-lain) tidak boleh dihapus.');
            }

            // Proteksi: jangan hapus jika sedang dipakai di transaksi atau kamus
            $usedInTransactions = Transaction::where('category_id', $id)->exists();
            $usedInKeywords     = KeywordDictionary::where('category_id', $id)->exists();

            if ($usedInTransactions || $usedInKeywords) {
                return back()->with('error', 'Kategori tidak bisa dihapus karena masih digunakan di Transaksi atau Kamus.');
            }

            $categoryName = $category->name;
            $category->delete();

            return redirect()->route('app.categories')
                ->with('success', "Kategori '{$categoryName}' berhasil dihapus.");
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->with('error', 'Kategori tidak ditemukan atau kamu tidak memiliki izin.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Terjadi kesalahan saat menghapus kategori.');
        }
    }
}
