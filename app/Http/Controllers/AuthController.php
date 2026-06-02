<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Category;
use App\Models\KeywordDictionary;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // ─── Login ────────────────────────────────────────────────────────────────

    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        $throttleKey = $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            abort(429, 'Terlalu banyak permintaan.', ['Retry-After' => $seconds]);
        }

        $remember = $request->boolean('remember');

        if (Auth::attempt($request->only('email', 'password'), $remember)) {
            RateLimiter::clear($throttleKey);
            
            // Logout from other devices automatically
            Auth::logoutOtherDevices($request->password);
            
            $request->session()->regenerate();
            return redirect()->intended(route('app.workspace'));
        }

        RateLimiter::hit($throttleKey, 900); // Block for 15 minutes (900 seconds)

        throw ValidationException::withMessages([
            'email' => 'Kredensial yang kamu masukkan tidak cocok dengan data kami.',
        ]);
    }

    // ─── Register ─────────────────────────────────────────────────────────────

    public function register(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'confirmed', PasswordRule::min(8)->letters()->mixedCase()->numbers()->symbols()->uncompromised()],
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $this->seedDefaultCategories($user->id);

        // Kirim event Registered → memicu email verifikasi
        event(new Registered($user));

        // Login user (tapi belum verified, akses protected route akan ditolak)
        Auth::login($user);

        // Redirect ke halaman check-email, BUKAN workspace
        return redirect()->route('auth.check-email');
    }

    // ─── Logout ───────────────────────────────────────────────────────────────

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    // ─── Forgot Password ──────────────────────────────────────────────────────

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        $throttleKey = 'forgot-password|' . $request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors(['email' => "Terlalu banyak permintaan reset. Tunggu {$seconds} detik."]);
        }
        RateLimiter::hit($throttleKey, 60);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    // ─── Reset Password ───────────────────────────────────────────────────────

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)->letters()->mixedCase()->numbers()->symbols()->uncompromised()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));
                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', 'Kata sandimu berhasil direset! Silakan masuk.')
            : back()->withErrors(['email' => [__($status)]]);
    }

    // ─── Resend Verification Email ────────────────────────────────────────────

    public function resendVerification(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('app.workspace');
        }

        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', 'Tautan verifikasi baru telah dikirim ke emailmu!');
    }

    // ─── Seeder ───────────────────────────────────────────────────────────────

    private function seedDefaultCategories(int $userId)
    {
        $categories = [
            ['name' => 'Makanan',     'type' => 'expense', 'is_default' => false, 'keywords' => ['makan', 'nasi', 'warteg', 'warung', 'ayam', 'bakso', 'mie', 'soto', 'indomie', 'rendang', 'restoran']],
            ['name' => 'Minuman',     'type' => 'expense', 'is_default' => false, 'keywords' => ['kopi', 'coffee', 'teh', 'minuman', 'jus', 'bubble', 'boba', 'susu', 'es', 'starbucks', 'jahe']],
            ['name' => 'Transport',   'type' => 'expense', 'is_default' => false, 'keywords' => ['bensin', 'pertamax', 'solar', 'parkir', 'grab', 'gojek', 'ojek', 'bus', 'kereta', 'toll', 'tol', 'angkot']],
            ['name' => 'Hiburan',     'type' => 'expense', 'is_default' => false, 'keywords' => ['game', 'topup', 'netflix', 'spotify', 'bioskop', 'cinema', 'main', 'hiburan', 'steam', 'nonton']],
            ['name' => 'Belanja',     'type' => 'expense', 'is_default' => false, 'keywords' => ['beli', 'belanja', 'shopee', 'tokopedia', 'lazada', 'indomaret', 'alfamart', 'minimarket', 'baju', 'sepatu', 'tas']],
            ['name' => 'Utilitas',    'type' => 'expense', 'is_default' => false, 'keywords' => ['listrik', 'pln', 'token', 'air', 'pdam', 'internet', 'wifi', 'pulsa', 'data', 'indihome', 'telkom']],
            ['name' => 'Kesehatan',   'type' => 'expense', 'is_default' => false, 'keywords' => ['obat', 'dokter', 'apotek', 'klinik', 'vitamin', 'rumah sakit', 'rs', 'puskesmas', 'konsultasi']],
            ['name' => 'Pendidikan',  'type' => 'expense', 'is_default' => false, 'keywords' => ['buku', 'kursus', 'les', 'sekolah', 'kuliah', 'biaya', 'pendidikan', 'jurnal', 'spp', 'tuition']],
            ['name' => 'Lain-lain',   'type' => 'expense', 'is_default' => true,  'keywords' => []],
            ['name' => 'Gaji',        'type' => 'income',  'is_default' => false, 'keywords' => ['gaji', 'salary', 'upah', 'honor', 'honorarium', 'payroll']],
            ['name' => 'Freelance',   'type' => 'income',  'is_default' => false, 'keywords' => ['freelance', 'proyek', 'project', 'klien', 'client', 'jasa']],
            ['name' => 'Pemasukan Lain', 'type' => 'income', 'is_default' => false, 'keywords' => ['cair', 'terima', 'transfer masuk', 'bonus', 'reward', 'cashback', 'refund', 'kembali']],
        ];

        foreach ($categories as $catData) {
            $cat = Category::create([
                'user_id'    => $userId,
                'name'       => $catData['name'],
                'type'       => $catData['type'],
                'is_default' => $catData['is_default'],
            ]);

            foreach ($catData['keywords'] as $kw) {
                KeywordDictionary::create([
                    'user_id'     => $userId,
                    'category_id' => $cat->id,
                    'keyword'     => $kw,
                ]);
            }
        }
    }
}
