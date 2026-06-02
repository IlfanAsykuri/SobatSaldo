<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InsightController extends Controller
{
    public function index(Request $request)
    {
        try {
            $userId = Auth::id();
            $now    = Carbon::now();

            // Filter bulan dari request
            $month = (int) $request->get('month', $now->month);
            $year  = (int) $request->get('year',  $now->year);

            // ─ Ringkasan Keuangan ──────────────────────────────────────────
            $totalIncome  = Transaction::forUser($userId)->forMonth($month, $year)->where('type', 'income')->sum('amount');
            $rawExpense   = Transaction::forUser($userId)->forMonth($month, $year)->where('type', 'expense')->sum('amount');
            $totalRefund  = Transaction::forUser($userId)->forMonth($month, $year)->where('type', 'refund')->sum('amount');
            $totalExpense = max($rawExpense - $totalRefund, 0);
            $netSaving    = $totalIncome - $totalExpense;

            // ─ Safe-to-Spend Harian ───────────────────────────────────────
            $daysInMonth   = Carbon::createFromDate($year, $month)->daysInMonth;
            $daysRemaining = max($daysInMonth - $now->day, 1);
            $dailySafe     = max($netSaving / $daysRemaining, 0);
            $avgPerDay     = $month === $now->month ? ($totalExpense / max($now->day, 1)) : ($totalExpense / $daysInMonth);

            // ─ Top 3 Kategori Pengeluaran (Donut Chart) ───────────────────
            $topCategories = Transaction::forUser($userId)
                ->forMonth($month, $year)
                ->where('type', 'expense')
                ->select('category_id', DB::raw('SUM(amount) as total'))
                ->groupBy('category_id')
                ->orderByDesc('total')
                ->limit(3)
                ->with('category')
                ->get();

            // Hitung persentase
            $totalCatExpense = $topCategories->sum('total');
            $topCategories = $topCategories->map(function ($item) use ($totalCatExpense) {
                $item->percentage = $totalCatExpense > 0
                    ? round(($item->total / $totalCatExpense) * 100)
                    : 0;
                return $item;
            });

            // ─ Bar Chart Harian (7 hari terakhir) ─────────────────────────
            $dailyExpenses = Transaction::forUser($userId)
                ->where('type', 'expense')
                ->where('created_at', '>=', Carbon::now()->subDays(6)->startOfDay())
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(amount) as total'))
                ->groupBy('date')
                ->orderBy('date')
                ->pluck('total', 'date');

            // Lengkapi 7 hari (isi 0 jika tidak ada transaksi)
            $last7Days = collect(range(6, 0))->map(function ($daysAgo) use ($dailyExpenses, $dailySafe) {
                $date  = Carbon::now()->subDays($daysAgo)->toDateString();
                $total = (float) ($dailyExpenses[$date] ?? 0);
                return [
                    'label'  => Carbon::parse($date)->translatedFormat('D'),
                    'date'   => $date,
                    'total'  => $total,
                    'is_over' => $total > ($dailySafe > 0 ? $dailySafe : 150000),
                ];
            });

            // ─ Pencapaian (Stats) ─────────────────────────────────────────
            $totalTransactions = Transaction::forUser($userId)->forMonth($month, $year)->where('type', '!=', 'transfer')->count();
            $streak = $this->calculateStreak($userId);
            $walletCount = \App\Models\Wallet::where('user_id', $userId)->count();
            $categoryCount = $topCategories->count();

            // ─ Pesan Storytelling ─────────────────────────────────────────
            $prevMonth        = Carbon::createFromDate($year, $month)->subMonth();
            $prevRawExpense   = Transaction::forUser($userId)
                ->forMonth($prevMonth->month, $prevMonth->year)
                ->where('type', 'expense')
                ->sum('amount');
            $prevRefund       = Transaction::forUser($userId)
                ->forMonth($prevMonth->month, $prevMonth->year)
                ->where('type', 'refund')
                ->sum('amount');
            $prevExpense      = max($prevRawExpense - $prevRefund, 0);

            $storyline = $this->buildStoryline($totalExpense, $prevExpense, $netSaving);

            // ─ Daftar bulan untuk dropdown ────────────────────────────────
            $availableMonths = Transaction::forUser($userId)
                ->select(DB::raw('YEAR(created_at) as year'), DB::raw('MONTH(created_at) as month'))
                ->groupBy('year', 'month')
                ->orderByDesc('year')->orderByDesc('month')
                ->get()
                ->map(fn($r) => [
                    'year'  => $r->year,
                    'month' => $r->month,
                    'label' => Carbon::createFromDate($r->year, $r->month)->translatedFormat('F Y'),
                ]);

            return view('app.insights', compact(
                'totalIncome', 'totalExpense', 'netSaving', 'dailySafe', 'avgPerDay',
                'topCategories', 'totalCatExpense', 'last7Days',
                'totalTransactions', 'streak', 'walletCount', 'categoryCount',
                'storyline', 'availableMonths', 'month', 'year'
            ));
        } catch (\Throwable $e) {
            return back()->with('error', 'Terjadi kesalahan saat memuat insights. Silakan coba lagi.');
        }
    }

    private function buildStoryline(float $currentExpense, float $prevExpense, float $netSaving): array
    {
        if ($prevExpense <= 0) {
            return ['emoji' => '👋', 'text' => 'Selamat datang di SobatSaldo! Mulai catat transaksimu sekarang.', 'positive' => true];
        }

        $diff    = $currentExpense - $prevExpense;
        $pct     = round(abs($diff / $prevExpense) * 100);
        $savings = number_format($netSaving, 0, ',', '.');

        if ($netSaving <= 0) {
            return ['emoji' => '⚠️', 'text' => "Pengeluaran bulan ini melebihi pemasukan. Tetap semangat, evaluasi bisa jadi awal yang baik!", 'positive' => false];
        }

        if ($diff < 0) {
            return ['emoji' => '🎉', 'text' => "Pengeluaranmu {$pct}% lebih rendah dari bulan lalu. Kamu hemat Rp {$savings}!", 'positive' => true];
        }

        if ($diff > 0) {
            return ['emoji' => '📊', 'text' => "Pengeluaran naik {$pct}% dibanding bulan lalu. Saldo bersih: Rp {$savings}.", 'positive' => false];
        }

        return ['emoji' => '✅', 'text' => "Pengeluaran bulan ini konsisten. Saldo bersih: Rp {$savings}.", 'positive' => true];
    }

    private function calculateStreak(int $userId): int
    {
        $dates = Transaction::where('user_id', $userId)
            ->where('type', '!=', 'transfer')
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
