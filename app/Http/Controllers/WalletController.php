<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWalletRequest;
use App\Http\Requests\UpdateWalletRequest;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    // ─── Index ────────────────────────────────────────────────────────────────

    public function index()
    {
        try {
            $userId = Auth::id();

            // IDOR: hanya wallet milik user ini
            $wallets = Wallet::where('user_id', $userId)->get();

            // Hitung total saldo semua wallet
            $totalBalance = $wallets->sum(fn($w) => $w->balance);

            // Aktivitas terbaru (lintas semua wallet)
            $activities = Transaction::with(['wallet', 'category'])
                ->where('user_id', $userId)
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();

            return view('app.wallet', compact('wallets', 'totalBalance', 'activities'));
        } catch (\Throwable $e) {
            return back()->with('error', 'Terjadi kesalahan saat memuat dompet.');
        }
    }

    // ─── Store ────────────────────────────────────────────────────────────────

    public function store(StoreWalletRequest $request)
    {
        try {
            $wallet = Wallet::create([
                'user_id'        => Auth::id(),
                'name'           => $request->name,
                'type'           => $request->type,
                'color_theme'    => $request->color_theme,
                'account_number' => $request->account_number,
            ]);

            return redirect()->route('app.wallet')
                ->with('success', "Dompet '{$wallet->name}' berhasil ditambahkan! 💳");
        } catch (\Throwable $e) {
            return back()->with('error', 'Terjadi kesalahan saat menyimpan dompet. Silakan coba lagi.');
        }
    }

    // ─── Update ───────────────────────────────────────────────────────────────

    public function update(UpdateWalletRequest $request, int $id)
    {
        try {
            // IDOR protection
            $wallet = Wallet::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $wallet->update($request->only('name', 'type', 'color_theme', 'account_number'));

            return redirect()->route('app.wallet')
                ->with('success', "Dompet '{$wallet->name}' berhasil diperbarui.");
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            abort(404, 'Dompet tidak ditemukan.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Terjadi kesalahan saat memperbarui dompet.');
        }
    }

    // ─── Destroy ──────────────────────────────────────────────────────────────

    public function destroy(Request $request, int $id)
    {
        try {
            // IDOR protection
            $wallet = Wallet::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $transactionCount = Transaction::where('wallet_id', $id)
                ->where('user_id', Auth::id())
                ->count();

            if ($transactionCount > 0) {
                // Cek apakah user ingin re-assign ke dompet lain
                if ($request->filled('reassign_to')) {
                    $targetWallet = Wallet::where('id', $request->reassign_to)
                        ->where('user_id', Auth::id())
                        ->first();

                    if ($targetWallet) {
                        Transaction::where('wallet_id', $id)
                            ->where('user_id', Auth::id())
                            ->update(['wallet_id' => $targetWallet->id]);
                    }
                } else {
                    return back()->with('error',
                        "Dompet '{$wallet->name}' memiliki {$transactionCount} transaksi. " .
                        "Pindahkan transaksi terlebih dahulu sebelum menghapus dompet, " .
                        "atau pilih dompet tujuan untuk memindahkan secara otomatis."
                    )->with('wallet_has_transactions', [
                        'wallet_id'    => $id,
                        'wallet_name'  => $wallet->name,
                        'count'        => $transactionCount,
                    ]);
                }
            }

            $walletName = $wallet->name;
            $wallet->delete();

            return redirect()->route('app.wallet')
                ->with('success', "Dompet '{$walletName}' berhasil dihapus.");
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            abort(404, 'Dompet tidak ditemukan.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Terjadi kesalahan saat menghapus dompet.');
        }
    }
}
