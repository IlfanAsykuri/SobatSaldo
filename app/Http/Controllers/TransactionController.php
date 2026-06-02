<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Models\Category;
use App\Models\KeywordDictionary;
use App\Models\Transaction;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    // ─── Index (Workspace dengan filter + paginate) ────────────────────────────

    public function index(Request $request)
    {
        $userId = Auth::id();
        $now    = Carbon::now();

        // Filter bulan/tahun
        $month = (int) $request->get('month', $now->month);
        $year  = (int) $request->get('year',  $now->year);

        // Query dasar dengan IDOR protection
        $query = Transaction::with(['category', 'wallet', 'toWallet'])
            ->forUser($userId)
            ->whereMonth('created_at', $month)
            ->whereYear('created_at',  $year);

        // Filter jenis transaksi
        if ($request->filled('type') && in_array($request->type, ['income', 'expense'])) {
            $query->where('type', $request->type);
        }

        // Filter teks pencarian
        if ($request->filled('search')) {
            $query->where('raw_text', 'like', '%' . e($request->search) . '%');
        }

        $transactions = $query->orderByDesc('created_at')->paginate(10)->withQueryString();

        // Metrik bulan ini (abaikan mutasi, hutang, piutang)
        $totalIncome  = Transaction::forUser($userId)->forMonth($month, $year)->where('type', 'income')->sum('amount');
        $rawExpense   = Transaction::forUser($userId)->forMonth($month, $year)->where('type', 'expense')->sum('amount');
        $totalRefund  = Transaction::forUser($userId)->forMonth($month, $year)->where('type', 'refund')->sum('amount');
        $totalExpense = max($rawExpense - $totalRefund, 0); // Pengeluaran dikurangi refund
        
        $daysRemaining = max(Carbon::createFromDate($year, $month)->daysInMonth - $now->day, 1);

        // Rollover Budget (Sisa Bulan Lalu)
        $startOfMonth = Carbon::createFromDate($year, $month)->startOfMonth();
        $pastIncome = Transaction::forUser($userId)
            ->where('created_at', '<', $startOfMonth)
            ->where('type', 'income')
            ->sum('amount');
        $pastExpenseRaw = Transaction::forUser($userId)
            ->where('created_at', '<', $startOfMonth)
            ->where('type', 'expense')
            ->sum('amount');
        $pastRefund = Transaction::forUser($userId)
            ->where('created_at', '<', $startOfMonth)
            ->where('type', 'refund')
            ->sum('amount');
        $pastExpense = max($pastExpenseRaw - $pastRefund, 0);
        $saldoBulanLalu = $pastIncome - $pastExpense;

        $user = Auth::user();
        if ($user->daily_limit > 0) {
            $spentToday = Transaction::forUser($userId)
                ->whereDate('created_at', Carbon::today())
                ->where('type', 'expense')
                ->sum('amount');
            $refundToday = Transaction::forUser($userId)
                ->whereDate('created_at', Carbon::today())
                ->where('type', 'refund')
                ->sum('amount');
            $spentToday = max($spentToday - $refundToday, 0);
            $safeToSpend = max($user->daily_limit - $spentToday, 0);
        } else {
            $safeToSpend = max((($saldoBulanLalu + $totalIncome) - $totalExpense) / $daysRemaining, 0);
        }

        // Streak 🔥
        $streak = $this->calculateStreak($userId);

        // Top categories untuk donut chart
        $topCategories = Transaction::forUser($userId)
            ->forMonth($month, $year)
            ->where('type', 'expense')
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->limit(3)
            ->with('category')
            ->get();

        // Wallet list (untuk dropdown di form)
        $wallets    = Wallet::where('user_id', $userId)->get();
        $categories = Category::where('user_id', $userId)->orderBy('name')->get();

        // Quick Habits
        $quickHabits = \App\Models\KeywordDictionary::where('user_id', $userId)
            ->where('is_quick_habit', true)
            ->limit(5)
            ->get();

        return view('app.workspace', compact(
            'transactions', 'totalIncome', 'totalExpense',
            'safeToSpend', 'streak', 'topCategories',
            'wallets', 'categories', 'month', 'year', 'quickHabits'
        ));
    }

    // ─── Store (AJAX / form POST) ──────────────────────────────────────────────

    public function store(StoreTransactionRequest $request)
    {
        try {
            $userId  = Auth::id();
            $inputMode = $request->input('inputMode', 'reguler');

            if ($inputMode === 'mutasi') {
                $transaction = Transaction::create([
                    'user_id'      => $userId,
                    'raw_text'     => 'Mutasi Dana',
                    'amount'       => $request->amount,
                    'type'         => 'transfer',
                    'wallet_id'    => $request->wallet_id,
                    'to_wallet_id' => $request->to_wallet_id,
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Mutasi berhasil dicatat! 🎉',
                    'data'    => [
                        'id'       => $transaction->id,
                        'raw_text' => $transaction->raw_text,
                        'amount'   => (float) $transaction->amount,
                        'type'     => $transaction->type,
                        'category' => 'Mutasi',
                        'time'     => $transaction->created_at->format('H:i'),
                    ],
                    'has_admin_fee' => false,
                    'admin_fee'     => 0,
                ]);
            }
            
            if ($inputMode === 'hutang') {
                $type = in_array($request->type, ['debt', 'collect_receivable']) ? 'income' : 'expense';
                
                $prefix = '';
                if ($request->type === 'debt') $prefix = 'Hutang dari ';
                if ($request->type === 'repay_debt') $prefix = 'Bayar hutang ke ';
                if ($request->type === 'receivable') $prefix = 'Beri pinjaman ke ';
                if ($request->type === 'collect_receivable') $prefix = 'Terima piutang dari ';
                
                $transaction = Transaction::create([
                    'user_id'      => $userId,
                    'raw_text'     => $prefix . $request->desc_hutang,
                    'desc_hutang'  => $request->desc_hutang,
                    'amount'       => $request->amount,
                    'type'         => $type,
                    'wallet_id'    => $request->wallet_id,
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Catatan hutang berhasil disimpan! 🎉',
                    'data'    => [
                        'id'       => $transaction->id,
                        'raw_text' => $transaction->raw_text,
                        'amount'   => (float) $transaction->amount,
                        'type'     => $transaction->type,
                        'category' => 'Hutang/Piutang',
                        'time'     => $transaction->created_at->format('H:i'),
                    ],
                    'has_admin_fee' => false,
                    'admin_fee'     => 0,
                ]);
            }

            // --- JIKA MODE REGULER ---
            $rawText = trim($request->raw_text);
            $type    = 'expense';
            $adminFee = 0;
            $originalRaw = $rawText;

            // 1. Ekstraksi Biaya Admin (+ angka di akhir)
            if (preg_match('/\+\s*([\d\.,]+k?)\s*$/i', $rawText, $adminMatch)) {
                $adminFee = $this->parseAmount($adminMatch[1]);
                $rawText  = trim(str_replace($adminMatch[0], '', $rawText));
            }

            // 2. Deteksi tipe transaksi
            $transferKeywords = ['tarik', 'withdraw', 'transfer', 'pindah', 'topup', 'cashout', 'setor', 'isi'];
            $incomeKeywords   = ['gaji', 'cair', 'masuk', 'terima', 'bonus', 'freelance', 'pemasukan'];
            
            $lowerRaw = strtolower($rawText);
            
            if (Str::contains($lowerRaw, $transferKeywords)) {
                $type = 'transfer';
            } elseif (Str::startsWith($rawText, '+') || Str::contains($lowerRaw, $incomeKeywords)) {
                $type    = 'income';
                $rawText = ltrim($rawText, '+');
            }

            // 3. Ekstraksi angka utama dari akhir string
            if (preg_match('/([\d\.,]+k?)\s*$/i', $rawText, $amountMatch)) {
                $amount  = $this->parseAmount($amountMatch[1]);
                $rawText = trim(preg_replace('/([\d\.,]+k?)\s*$/i', '', $rawText));
            } else {
                $amount = 0;
            }

            if (empty($rawText)) {
                $rawText = $originalRaw; // Fallback ke teks asli
            }

            // 4. Smart Dictionary Matching (hanya jika bukan transfer)
            $categoryId = null;
            $words      = array_filter(explode(' ', strtolower(preg_replace('/[^a-z0-9\s]/i', '', $rawText))));
            
            if ($type !== 'transfer') {
                foreach ($words as $word) {
                    if (strlen($word) < 2) continue;
                    $match = KeywordDictionary::forUser($userId)
                        ->where('keyword', $word)
                        ->with('category')
                        ->first();
                    if ($match) {
                        $categoryId = $match->category_id;
                        if ($match->category) {
                            $type = $match->category->type;
                        }
                        break;
                    }
                }
                // Fallback Default Category
                if (!$categoryId) {
                    $fallback = Category::where('user_id', $userId)->where('is_default', true)->first();
                    $categoryId = $fallback?->id;
                }
            }

            // 5. Smart Routing & Wallet Assignment
            $walletId   = null;
            $toWalletId = null;
            
            $explicitWalletId = $request->filled('wallet_id') ? $request->wallet_id : null;

            if ($type === 'transfer') {
                $matchedWalletId = $explicitWalletId;
                
                if (!$matchedWalletId) {
                    // Cari apakah ada kata yang cocok dengan nama dompet
                    foreach ($words as $word) {
                        if (strlen($word) < 2) continue;
                        $wMatch = Wallet::where('user_id', $userId)->where('name', 'like', '%' . $word . '%')->first();
                        if ($wMatch) {
                            $matchedWalletId = $wMatch->id;
                            break;
                        }
                    }
                }
                
                $cashWallet = Wallet::where('user_id', $userId)->where('type', 'cash')->first();
                $cashWalletId = $cashWallet?->id;
                
                // Mencegah error jika dompet cash tidak ada
                if (!$cashWalletId) {
                    $cashWalletId = Wallet::where('user_id', $userId)->where('id', '!=', $matchedWalletId)->first()?->id;
                }
                
                $isPushToWallet = Str::contains($lowerRaw, ['topup', 'setor', 'isi']);
                
                if ($isPushToWallet) {
                    // Push: Dari Cash ke Dompet (misal: setor bca)
                    $walletId = $cashWalletId;
                    $toWalletId = $matchedWalletId;
                } else {
                    // Pull: Dari Dompet ke Cash (misal: tarik bca)
                    $walletId = $matchedWalletId;
                    $toWalletId = $cashWalletId;
                }
                
                // Mencegah wallet_id sama dengan to_wallet_id
                if ($walletId == $toWalletId && $walletId != null) {
                    $otherWallet = Wallet::where('user_id', $userId)->where('id', '!=', $walletId)->first();
                    if ($isPushToWallet) {
                        $walletId = $otherWallet?->id;
                    } else {
                        $toWalletId = $otherWallet?->id; 
                    }
                }
            } else {
                $walletId = $explicitWalletId;
            }

            // Pastikan wallet asal milik user
            if ($walletId) {
                $wallet = Wallet::where('id', $walletId)->where('user_id', $userId)->first();
                $walletId = $wallet?->id;
            }

            // 6. Simpan transaksi utama
            $transaction = Transaction::create([
                'user_id'     => $userId,
                'category_id' => $categoryId,
                'wallet_id'   => $walletId,
                'to_wallet_id'=> $toWalletId,
                'raw_text'    => $rawText,
                'amount'      => $amount,
                'type'        => $type,
            ]);

            // 7. Simpan biaya admin (selalu expense)
            if ($adminFee > 0) {
                Transaction::create([
                    'user_id'     => $userId,
                    'category_id' => $categoryId,
                    'wallet_id'   => $walletId,
                    'raw_text'    => 'Biaya Admin',
                    'amount'      => $adminFee,
                    'type'        => 'expense',
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dicatat! 🎉',
                'data'    => [
                    'id'       => $transaction->id,
                    'raw_text' => $transaction->raw_text,
                    'amount'   => (float) $transaction->amount,
                    'type'     => $transaction->type,
                    'category' => $transaction->category->name ?? 'Lain-lain',
                    'time'     => $transaction->created_at->format('H:i'),
                ],
                'has_admin_fee' => $adminFee > 0,
                'admin_fee'     => $adminFee,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.',
            ], 500);
        }
    }

    // ─── Update (edit all fields) ──────────────────────────────────────────────

    public function update(Request $request, int $id)
    {
        try {
            // IDOR: pastikan transaksi milik user yang login
            $transaction = Transaction::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $request->validate([
                'raw_text'    => ['required', 'string', 'max:500'],
                'amount'      => ['required', 'numeric', 'min:0'],
                'type'        => ['required', 'in:income,expense,transfer'],
                'category_id' => ['nullable', 'exists:categories,id'],
                'wallet_id'   => ['nullable', 'exists:wallets,id'],
                'to_wallet_id'=> ['nullable', 'exists:wallets,id'],
            ]);

            // Verifikasi wallet juga milik user (IDOR)
            if ($request->filled('wallet_id')) {
                Wallet::where('id', $request->wallet_id)
                    ->where('user_id', Auth::id())
                    ->firstOrFail();
            }
            if ($request->filled('to_wallet_id')) {
                Wallet::where('id', $request->to_wallet_id)
                    ->where('user_id', Auth::id())
                    ->firstOrFail();
            }

            // Jika transfer, category_id = null
            $categoryId = $request->type === 'transfer' ? null : $request->category_id;
            
            // Jika bukan transfer, pastikan category_id ada
            if ($request->type !== 'transfer' && !$categoryId) {
                return response()->json(['success' => false, 'message' => 'Kategori harus diisi untuk pemasukan/pengeluaran.'], 422);
            }

            $transaction->update([
                'raw_text'    => $request->raw_text,
                'amount'      => $request->amount,
                'type'        => $request->type,
                'category_id' => $categoryId,
                'wallet_id'   => $request->wallet_id,
                'to_wallet_id'=> $request->to_wallet_id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil diperbarui.',
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return response()->json(['success' => false, 'message' => 'Transaksi tidak ditemukan.'], 404);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.'], 500);
        }
    }

    // ─── Destroy ──────────────────────────────────────────────────────────────

    public function destroy(int $id)
    {
        try {
            // IDOR protection
            $transaction = Transaction::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $transaction->delete();

            return response()->json(['success' => true, 'message' => 'Transaksi berhasil dihapus.']);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return response()->json(['success' => false, 'message' => 'Transaksi tidak ditemukan.'], 404);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem.'], 500);
        }
    }

    // ─── Update Category (Auto-Learn) ─────────────────────────────────────────

    public function updateCategory(Request $request, int $id)
    {
        try {
            $userId = Auth::id();

            // IDOR: pastikan transaksi milik user
            $transaction = Transaction::where('id', $id)
                ->where('user_id', $userId)
                ->firstOrFail();

            $request->validate([
                'category_id' => ['required', 'exists:categories,id'],
            ]);

            $oldCategoryId = $transaction->category_id;
            $newCategoryId = (int) $request->category_id;

            $transaction->update(['category_id' => $newCategoryId]);

            // Auto-Learn: hanya jika kategori berubah dari default (Lain-lain)
            $defaultCategory = Category::where('user_id', $userId)->where('is_default', true)->first();
            if ($oldCategoryId === $defaultCategory?->id && $newCategoryId !== $oldCategoryId) {
                $stopwords    = ['di', 'ke', 'dari', 'bayar', 'beli', 'yang', 'dan', 'untuk', 'buat', 'sama', 'dengan', 'karena', 'atau'];
                $cleanedText  = preg_replace('/[^a-zA-Z\s]/', '', strtolower($transaction->raw_text));
                $words        = array_filter(explode(' ', $cleanedText));
                $validWords   = array_diff($words, $stopwords);
                $dominantWords = array_slice(array_values($validWords), 0, 2);

                foreach ($dominantWords as $word) {
                    if (strlen($word) > 2) {
                        // Simpan sebagai keyword milik user ini
                        KeywordDictionary::firstOrCreate(
                            ['keyword' => $word, 'user_id' => $userId],
                            ['category_id' => $newCategoryId]
                        );
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Kategori diperbarui dan sistem belajar! 🧠',
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return response()->json(['success' => false, 'message' => 'Transaksi tidak ditemukan.'], 404);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem.'], 500);
        }
    }

    // ─── Helper: Parse Amount ─────────────────────────────────────────────────

    private function parseAmount(string $string): float
    {
        $string = strtolower(str_replace(['.', ','], '', trim($string)));
        if (str_ends_with($string, 'k')) {
            return (float) rtrim($string, 'k') * 1000;
        }
        return (float) $string;
    }

    // ─── Helper: Hitung Streak ────────────────────────────────────────────────

    private function calculateStreak(int $userId): int
    {
        $dates = Transaction::where('user_id', $userId)
            ->select(DB::raw('DATE(created_at) as date'))
            ->groupBy('date')
            ->orderByDesc('date')
            ->pluck('date');

        $streak    = 0;
        $checkDate = Carbon::today();

        foreach ($dates as $date) {
            $parsedDate = Carbon::parse($date);
            if ($parsedDate->equalTo($checkDate) || $parsedDate->equalTo($checkDate->copy()->subDay())) {
                $streak++;
                $checkDate = $parsedDate;
            } else {
                break;
            }
        }
        return $streak;
    }
}
