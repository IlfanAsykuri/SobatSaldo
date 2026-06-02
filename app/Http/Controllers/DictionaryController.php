<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKeywordRequest;
use App\Http\Requests\UpdateKeywordRequest;
use App\Models\Category;
use App\Models\KeywordDictionary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DictionaryController extends Controller
{
    // ─── Index ────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        try {
            $userId = Auth::id();

            // Hanya keyword milik user ini + keyword global (user_id = null)
            $query = KeywordDictionary::with('category')
                ->forUser($userId)
                ->orderByDesc('updated_at');

            // Filter pencarian
            if ($request->filled('search')) {
                $query->where('keyword', 'like', '%' . e($request->search) . '%');
            }

            // Filter kategori
            if ($request->filled('category_id')) {
                $query->where('category_id', (int) $request->category_id);
            }

            $keywords   = $query->paginate(15)->withQueryString();
            $categories = Category::where('user_id', $userId)->orderBy('name')->get();

            return view('app.dictionary', compact('keywords', 'categories'));
        } catch (\Throwable $e) {
            return back()->with('error', 'Terjadi kesalahan saat memuat kamus kata kunci.');
        }
    }

    // ─── Store ────────────────────────────────────────────────────────────────

    public function store(StoreKeywordRequest $request)
    {
        try {
            KeywordDictionary::create([
                'user_id'     => Auth::id(),
                'keyword'     => strtolower(trim($request->keyword)),
                'category_id' => $request->category_id,
            ]);

            return redirect()->route('app.dictionary')
                ->with('success', "Kata kunci '{$request->keyword}' berhasil ditambahkan ke kamus! 📖");
        } catch (\Throwable $e) {
            return back()->with('error', 'Terjadi kesalahan saat menyimpan kata kunci.')
                ->withInput();
        }
    }

    // ─── Update ───────────────────────────────────────────────────────────────

    public function update(UpdateKeywordRequest $request, int $id)
    {
        try {
            // IDOR: pastikan keyword milik user yang login
            $keyword = KeywordDictionary::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $keyword->update([
                'keyword'     => strtolower(trim($request->keyword)),
                'category_id' => $request->category_id,
            ]);

            return redirect()->route('app.dictionary')
                ->with('success', 'Kata kunci berhasil diperbarui! ✅');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            // Kata kunci global (user_id = null) tidak boleh diedit user biasa
            return back()->with('error', 'Kamu tidak memiliki izin untuk mengedit kata kunci ini.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Terjadi kesalahan saat memperbarui kata kunci.');
        }
    }

    // ─── Destroy ──────────────────────────────────────────────────────────────

    public function destroy(int $id)
    {
        try {
            // IDOR: hanya keyword milik user sendiri yang bisa dihapus
            $keyword = KeywordDictionary::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $keywordName = $keyword->keyword;
            $keyword->delete();

            return redirect()->route('app.dictionary')
                ->with('success', "Kata kunci '{$keywordName}' berhasil dihapus.");
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return back()->with('error', 'Kamu tidak memiliki izin untuk menghapus kata kunci ini.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Terjadi kesalahan saat menghapus kata kunci.');
        }
    }
    // ─── Toggle Quick Habit ───────────────────────────────────────────────────

    public function toggleQuickHabit(int $id)
    {
        try {
            $keyword = KeywordDictionary::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            if (!$keyword->is_quick_habit) {
                // Mau diaktifkan, cek batas maksimal 5
                $count = KeywordDictionary::where('user_id', Auth::id())->where('is_quick_habit', true)->count();
                if ($count >= 5) {
                    return back()->with('error', 'Maksimal 5 Quick Habits. Hapus yang lain dulu.');
                }
            }

            $keyword->update([
                'is_quick_habit' => !$keyword->is_quick_habit
            ]);

            return back()->with('success', 'Status Quick Habit berhasil diubah! ⚡');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return back()->with('error', 'Kata kunci tidak ditemukan atau kamu tidak memiliki izin.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Terjadi kesalahan saat mengubah status Quick Habit.');
        }
    }
}
