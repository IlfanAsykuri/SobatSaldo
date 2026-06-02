<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\InsightController;
use App\Http\Controllers\DictionaryController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\CategoryController;

/*
|--------------------------------------------------------------------------
| SobatSaldo — Web Routes
|--------------------------------------------------------------------------
*/

// ── Root ────────────────────────────────────────────────────────────────────
Route::get('/', function () {
    return Auth::check() ? redirect()->route('app.workspace') : redirect()->route('login');
});

// ── Auth (hanya untuk tamu, belum login) ────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',    fn() => view('auth.login'))->name('login');
    Route::post('/login',   [AuthController::class, 'login'])->name('auth.login.post');

    Route::get('/register', fn() => view('auth.register'))->name('register');
    Route::post('/register',[AuthController::class, 'register'])->name('auth.register.post');

    Route::get('/forgot-password', fn() => view('auth.forgot-password'))->name('password.request');
    Route::post('/forgot-password',[AuthController::class, 'forgotPassword'])->name('password.email');

    Route::get('/reset-password/{token}', function (string $token) {
        $email = request()->email;
        $user = \App\Models\User::where('email', $email)->first();
        
        if (!$user || !\Illuminate\Support\Facades\Password::broker()->tokenExists($user, $token)) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Link reset password sudah tidak berlaku atau kadaluarsa. Silakan minta link baru.']);
        }

        return view('auth.reset-password', ['token' => $token]);
    })->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

// ── Email Verification (harus login, belum verified) ────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/check-email', fn() => view('auth.check-email'))->name('auth.check-email');

    Route::get('/email/verify/{id}/{hash}', function (\Illuminate\Foundation\Auth\EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect()->route('app.workspace')->with('success', 'Emailmu berhasil diverifikasi! Selamat datang di SobatSaldo 🎉');
    })->middleware('signed')->name('verification.verify');

    Route::post('/email/verification-notification', [AuthController::class, 'resendVerification'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// ── Core App — Protected (login + email verified) ────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {

    // ── Workspace ─────────────────────────────────────────────────────────────
    Route::get('/workspace', [TransactionController::class, 'index'])->name('app.workspace');

    // Transactions CRUD (AJAX + form)
    Route::post('/transactions',          [TransactionController::class, 'store'])->name('transactions.store');
    Route::put('/transactions/{id}',      [TransactionController::class, 'update'])->name('transactions.update');
    Route::delete('/transactions/{id}',   [TransactionController::class, 'destroy'])->name('transactions.destroy');
    Route::patch('/transactions/{id}/category', [TransactionController::class, 'updateCategory'])->name('transactions.updateCategory');

    // ── Wallet ────────────────────────────────────────────────────────────────
    Route::get('/wallet',              [WalletController::class, 'index'])->name('app.wallet');
    Route::post('/wallet',             [WalletController::class, 'store'])->name('wallets.store');
    Route::put('/wallet/{id}',         [WalletController::class, 'update'])->name('wallets.update');
    Route::delete('/wallet/{id}',      [WalletController::class, 'destroy'])->name('wallets.destroy');

    // ── Insights ──────────────────────────────────────────────────────────────
    Route::get('/insights', [InsightController::class, 'index'])->name('app.insights');

    // ── Dictionary ────────────────────────────────────────────────────────────
    Route::get('/dictionary', [DictionaryController::class, 'index'])->name('app.dictionary');
    Route::post('/dictionary', [DictionaryController::class, 'store'])->name('dictionary.store');
    Route::put('/dictionary/{id}', [DictionaryController::class, 'update'])->name('dictionary.update');
    Route::delete('/dictionary/{id}', [DictionaryController::class, 'destroy'])->name('dictionary.destroy');
    Route::patch('/dictionary/{id}/quick-habit', [DictionaryController::class, 'toggleQuickHabit'])->name('dictionary.quick-habit');

    // ── Categories ────────────────────────────────────────────────────────────
    Route::get('/categories', [CategoryController::class, 'index'])->name('app.categories');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // ── Settings ──────────────────────────────────────────────────────────────
    Route::get('/settings',                  [SettingsController::class, 'index'])->name('app.settings');
    Route::put('/settings/profile',          [SettingsController::class, 'updateProfile'])->name('settings.profile');
    Route::put('/settings/password',         [SettingsController::class, 'updatePassword'])->name('settings.password');
    Route::get('/settings/export/csv',       [SettingsController::class, 'exportCsv'])->name('settings.export.csv');
    Route::get('/settings/export/excel',     [SettingsController::class, 'exportExcel'])->name('settings.export.excel');
});

// ── Legacy / backward compat redirects ────────────────────────────────────────
Route::get('/app/workspace', fn() => redirect()->route('app.workspace'))->name('app.workspace.legacy');
Route::get('/auth/login',    fn() => redirect()->route('login'))->name('auth.login');
Route::get('/auth/register', fn() => redirect()->route('register'))->name('auth.register');

// ── Error Page Previews (dev only) ────────────────────────────────────────────
Route::prefix('preview/errors')->name('preview.errors.')->group(function () {
    Route::get('/404', fn() => view('errors.404'))->name('404');
    Route::get('/429', fn() => view('errors.429'))->name('429');
    Route::get('/500', fn() => view('errors.500'))->name('500');
});
