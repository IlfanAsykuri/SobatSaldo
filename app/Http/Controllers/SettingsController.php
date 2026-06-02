<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransactionsExport;

class SettingsController extends Controller
{
    // ─── Index ────────────────────────────────────────────────────────────────

    public function index()
    {
        return view('app.settings', ['user' => Auth::user()]);
    }

    // ─── Update Profile ───────────────────────────────────────────────────────

    public function updateProfile(UpdateProfileRequest $request)
    {
        try {
            $user = Auth::user();
            $user->update([
                'name'        => $request->name,
                'email'       => $request->email,
                'daily_limit' => $request->daily_limit ?? 0,
            ]);

            // Jika email berubah, reset verifikasi
            if ($user->wasChanged('email')) {
                $user->email_verified_at = null;
                $user->save();
                $user->sendEmailVerificationNotification();
                return redirect()->route('auth.check-email')
                    ->with('success', 'Profil diperbarui. Silakan verifikasi email baru kamu.');
            }

            return back()->with('success', 'Profil berhasil diperbarui! ✅');
        } catch (\Throwable $e) {
            return back()->with('error', 'Terjadi kesalahan saat memperbarui profil.');
        }
    }

    // ─── Update Password ──────────────────────────────────────────────────────

    public function updatePassword(UpdatePasswordRequest $request)
    {
        try {
            Auth::user()->update([
                'password' => Hash::make($request->password),
            ]);

            return back()->with('success', 'Kata sandi berhasil diubah! 🔒');
        } catch (\Throwable $e) {
            return back()->with('error', 'Terjadi kesalahan saat mengubah kata sandi.');
        }
    }

    // ─── Export CSV ───────────────────────────────────────────────────────────

    public function exportCsv()
    {
        try {
            $userId       = Auth::id();
            $transactions = Transaction::with(['category', 'wallet'])
                ->where('user_id', $userId)
                ->orderByDesc('created_at')
                ->get();

            $filename = 'sobatsaldo-transaksi-' . now()->format('Ymd-His') . '.csv';

            $headers = [
                'Content-Type'        => 'text/csv; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                'Pragma'              => 'no-cache',
            ];

            $callback = function () use ($transactions) {
                $handle = fopen('php://output', 'w');
                // BOM untuk Excel agar bisa baca UTF-8
                fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

                // Header row
                fputcsv($handle, ['Tanggal', 'Deskripsi', 'Jenis', 'Kategori', 'Dompet', 'Nominal (Rp)']);

                foreach ($transactions as $trx) {
                    fputcsv($handle, [
                        $trx->created_at->format('d/m/Y H:i'),
                        $trx->raw_text,
                        $trx->type === 'income' ? 'Pemasukan' : 'Pengeluaran',
                        $trx->category->name ?? 'Lain-lain',
                        $trx->wallet->name ?? '-',
                        number_format((float) $trx->amount, 0, ',', '.'),
                    ]);
                }

                fclose($handle);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Throwable $e) {
            return back()->with('error', 'Terjadi kesalahan saat mengekspor data CSV.');
        }
    }

    // ─── Export Excel ─────────────────────────────────────────────────────────

    public function exportExcel()
    {
        try {
            $filename = 'sobatsaldo-transaksi-' . now()->format('Ymd-His') . '.xlsx';
            return Excel::download(new TransactionsExport(Auth::id()), $filename);
        } catch (\Throwable $e) {
            return back()->with('error', 'Terjadi kesalahan saat mengekspor data Excel.');
        }
    }
}
